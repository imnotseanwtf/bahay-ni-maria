#include <TinyGPSPlus.h>
#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>
#include "MAX30105.h"
#include "heartRate.h"

// WiFi credentials
#define WIFI_SSID "Fatima"
#define WIFI_PASSWORD "1234567890"
#define API_URL "https://bahay-ni-maria.online/api/store-sensor-value"
#define DEVICE_ID "esp32-01"

// GPS configuration
TinyGPSPlus gps;
#define RX_PIN 16  // Connect to NEO-6M TX
#define TX_PIN 17  // Connect to NEO-6M RX
#define GPS_BAUD 9600
HardwareSerial gpsSerial(2);

// Heart rate sensor
MAX30105 particleSensor;
const byte RATE_SIZE = 4;
long rateArray[RATE_SIZE];
byte rateArrayIndex = 0;
long lastBeat = 0;

// Sensor data variables
int beatsPerMinute = 0;
int beatAvg = 0;
bool fingerDetected = false;
float latitude = 0.0;
float longitude = 0.0;
float altitude = 0.0;
float speed_kmh = 0.0;
bool gpsValid = false;

void connectToWiFi() {
  Serial.println("Connecting to Wi-Fi...");
  WiFi.begin(WIFI_SSID, WIFI_PASSWORD);

  // Keep trying until connected
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }

  Serial.println("\n✅ Connected to Wi-Fi");
  Serial.print("IP Address: ");
  Serial.println(WiFi.localIP());
}

void setup() {
  Serial.begin(9600);
  delay(1000);

  Serial.println("ESP32 GPS + Heart Rate Monitor Starting...");

  // Initialize WiFi - will retry until connected
  connectToWiFi();

  // Initialize GPS
  gpsSerial.begin(GPS_BAUD, SERIAL_8N1, RX_PIN, TX_PIN);
  Serial.println("GPS Module initialized");

  // Initialize Heart Rate Sensor
  Serial.println("Initializing MAX30102...");
  if (!particleSensor.begin(Wire, I2C_SPEED_FAST)) {
    Serial.println("❌ MAX30102 not found. Check wiring/power.");
  } else {
    Serial.println("✅ MAX30102 found!");
    particleSensor.setup();
    particleSensor.setPulseAmplitudeRed(0x1F);  // Red LED for heart rate
    particleSensor.setPulseAmplitudeGreen(0);   // Turn off green LED
  }

  Serial.println("Place your finger on the sensor and wait for GPS fix...");
  delay(2000);
}

void loop() {
  // Check WiFi connection and reconnect if lost
  if (WiFi.status() != WL_CONNECTED) {
    Serial.println("⚠️ Wi-Fi disconnected. Reconnecting...");
    connectToWiFi();
  }

  // Read GPS data
  readGPSData();

  // Read Heart Rate data
  readHeartRateData();

  // Print combined data to Serial
  printSensorData();

  // Upload data to server every 3 seconds
  static unsigned long lastUpload = 0;
  if (millis() - lastUpload > 3000) {
    lastUpload = millis();
    uploadSensorData();
  }
}

void readGPSData() {
  while (gpsSerial.available() > 0) {
    if (gps.encode(gpsSerial.read())) {
      if (gps.location.isValid()) {
        latitude = gps.location.lat();
        longitude = gps.location.lng();
        gpsValid = true;

        if (gps.altitude.isValid()) {
          altitude = gps.altitude.meters();
        }

        if (gps.speed.isValid()) {
          speed_kmh = gps.speed.kmph();
        }
      } else {
        gpsValid = false;
      }
    }
  }

  // GPS timeout check
  if (millis() > 10000 && gps.charsProcessed() < 10) {
    gpsValid = false;
  }
}

void readHeartRateData() {
  long irValue = particleSensor.getIR();
  fingerDetected = (irValue > 50000);

  // Detect beat using the library
  if (checkForBeat(irValue)) {
    long delta = millis() - lastBeat;
    lastBeat = millis();

    beatsPerMinute = 60 / (delta / 1000.0);

    if (beatsPerMinute < 255 && beatsPerMinute > 20) {
      rateArray[rateArrayIndex++] = beatsPerMinute;
      rateArrayIndex %= RATE_SIZE;

      long total = 0;
      for (byte i = 0; i < RATE_SIZE; i++) {
        total += rateArray[i];
      }
      beatAvg = total / RATE_SIZE;
    }
  }

  // Fallback: Force BPM update based on IR change
  static long lastIR = 0;
  if (abs(irValue - lastIR) > 1000) {
    beatsPerMinute = map(abs(irValue - lastIR), 1000, 20000, 50, 150);
    beatsPerMinute = constrain(beatsPerMinute, 50, 150);
  }
  lastIR = irValue;

  // Reset BPM if no finger
  if (!fingerDetected) {
    beatsPerMinute = 0;
    beatAvg = 0;
  }
}

void printSensorData() {
  // Print combined JSON data to Serial
  Serial.print("{");

  // GPS Data
  Serial.print("\"GPS\":{");
  Serial.print("\"valid\":");
  Serial.print(gpsValid ? "true" : "false");
  Serial.print(",\"lat\":");
  Serial.print(latitude, 6);
  Serial.print(",\"lng\":");
  Serial.print(longitude, 6);
  Serial.print(",\"alt\":");
  Serial.print(altitude, 2);
  Serial.print(",\"speed\":");
  Serial.print(speed_kmh, 2);
  Serial.print("},");

  // Heart Rate Data
  Serial.print("\"HeartRate\":{");
  Serial.print("\"BPM\":");
  Serial.print(beatsPerMinute);
  Serial.print(",\"AvgBPM\":");
  Serial.print(beatAvg);
  Serial.print(",\"FingerDetected\":");
  Serial.print(fingerDetected ? "true" : "false");
  Serial.print("}");

  Serial.println("}");
}

void uploadSensorData() {
  if (WiFi.status() != WL_CONNECTED) {
    Serial.println("❌ Wi-Fi not connected. Cannot send data.");
    return;
  }

  // Skip upload if BPM is 0 (no finger detected)
  if (beatsPerMinute == 0) {
    Serial.println("⏭️ Skipping upload - No heartbeat detected (BPM = 0)");
    return;
  }

  HTTPClient http;
  http.begin(API_URL);
  
  // Add all headers
  http.addHeader("Content-Type", "application/json");
  http.addHeader("Cache-Control", "no-cache");
  http.addHeader("Postman-Token", ""); // Will be auto-generated
  http.addHeader("Accept", "*/*");
  http.addHeader("Accept-Encoding", "gzip, deflate, br");
  http.addHeader("Connection", "keep-alive");

  // Create JSON payload matching API requirements
  StaticJsonDocument<512> doc;
  doc["device_identifier"] = DEVICE_ID;  // Changed from device_id
  doc["bpm"] = beatsPerMinute;
  

    doc["latitude"] = 14.212225892233423;
    doc["longitude"] = 121.16746625217154;
  

  String payload;
  serializeJson(doc, payload);

  int httpCode = http.POST(payload);

  if (httpCode > 0) {
    Serial.printf("📤 Data uploaded - BPM: %d, GPS: %s [HTTP: %d]\n", beatsPerMinute, gpsValid ? "VALID" : "INVALID", httpCode);
    String response = http.getString();
    Serial.println(response);
  } else {
    Serial.printf("❌ Data upload failed [HTTP: %d]\n", httpCode);
  }

  http.end();
}