#include <TinyGPSPlus.h>
#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>
#include "MAX30105.h"
#include "heartRate.h"

// WiFi credentials
#define WIFI_SSID "Doctora Wifi 2.4G"
#define WIFI_PASSWORD "pitterdoctorawifi"
#define API_URL "http://192.168.18.161/api/store-sensor-value"
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

void setup() {
  Serial.begin(9600);
  delay(1000);

  Serial.println("ESP32 GPS + Heart Rate Monitor Starting...");

  // Initialize WiFi
  Serial.println("Connecting to Wi-Fi...");
  WiFi.begin(WIFI_SSID, WIFI_PASSWORD);

  int attempt = 0;
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
    if (++attempt > 30) {
      Serial.println("\n❌ Wi-Fi Connection Failed!");
      break;
    }
  }
  
  if (WiFi.status() == WL_CONNECTED) {
    Serial.println("\n✅ Connected to Wi-Fi");
    Serial.print("IP Address: ");
    Serial.println(WiFi.localIP());
  }

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
    particleSensor.setPulseAmplitudeRed(0x1F); // Red LED for heart rate
    particleSensor.setPulseAmplitudeGreen(0);  // Turn off green LED
  }

  Serial.println("Place your finger on the sensor and wait for GPS fix...");
  delay(2000);
}

void loop() {
  // Read GPS data
  readGPSData();
  
  // Read heart rate data
  readHeartRateData();
  
  // Print combined sensor data
  printSensorData();
  
  // Send data to server every 3 seconds
  static unsigned long lastUpload = 0;
  if (millis() - lastUpload > 3000) {
    lastUpload = millis();
    sendDataToServer();
  }

  delay(100);
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
  
  // GPS data
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
  
  // Heart rate data
  Serial.print("\"HeartRate\":{");
  Serial.print("\"BPM\":");
  Serial.print(beatsPerMinute);
  Serial.print(",\"AvgBPM\":");
  Serial.print(beatAvg);
  Serial.print(",\"Finger\":");
  Serial.print(fingerDetected ? "true" : "false");
  Serial.print("}");
  
  Serial.println("}");
}

void sendDataToServer() {
  if (WiFi.status() != WL_CONNECTED) {
    Serial.println("❌ Wi-Fi not connected. Cannot send data.");
    return;
  }

  // Only send if we have valid heart rate data (GPS can be optional)
  if (!fingerDetected || beatsPerMinute < 30 || beatsPerMinute > 200) {
    Serial.printf("⏩ Skip upload - Finger: %s, BPM: %d\n", fingerDetected ? "YES" : "NO", beatsPerMinute);
    return;
  }

  HTTPClient http;
  http.begin(API_URL);
  http.addHeader(F("Content-Type"), F("application/json"));
  http.setTimeout(5000);

  // Create JSON payload with both GPS and heart rate data
  StaticJsonDocument<400> doc;
  doc["device_identifier"] = DEVICE_ID;
  doc["timestamp"] = millis();
  
  // Heart rate data
  doc["bpm"] = beatsPerMinute;
  doc["avg_bpm"] = beatAvg;
  doc["finger_detected"] = fingerDetected;
  
  // GPS data
  doc["gps_valid"] = gpsValid;
  if (gpsValid) {
    doc["latitude"] = latitude;
    doc["longitude"] = longitude;
  }

  String payload;
  serializeJson(doc, payload);

  int httpCode = http.POST(payload);

  if (httpCode > 0) {
    Serial.printf("📤 Data uploaded - BPM: %d, GPS: %s [HTTP: %d]\n", 
                  beatsPerMinute, gpsValid ? "VALID" : "INVALID", httpCode);
    
    if (httpCode == 200) {
      String response = http.getString();
      Serial.println("✅ Server response: " + response);
    }
  } else {
    Serial.printf("❌ Upload error: %s\n", http.errorToString(httpCode).c_str());
  }

  http.end();
}

void displayDetailedInfo() {
  // Detailed GPS info (call this function manually if needed for debugging)
  Serial.println("\n=== DETAILED SENSOR INFO ===");
  
  if (gpsValid) {
    Serial.printf("📍 GPS Location: %.6f, %.6f\n", latitude, longitude);
    Serial.printf("🏔️  Altitude: %.2f meters\n", altitude);
    Serial.printf("🚗 Speed: %.2f km/h\n", speed_kmh);
    
    if (gps.date.isValid() && gps.time.isValid()) {
      Serial.printf("📅 Date: %04d-%02d-%02d\n", gps.date.year(), gps.date.month(), gps.date.day());
      Serial.printf("🕐 Time: %02d:%02d:%02d UTC\n", gps.time.hour(), gps.time.minute(), gps.time.second());
    }
  } else {
    Serial.println("📍 GPS: No valid fix");
  }
  
  if (fingerDetected) {
    Serial.printf("💓 Heart Rate: %d BPM (Avg: %d BPM)\n", beatsPerMinute, beatAvg);
  } else {
    Serial.println("💓 Heart Rate: No finger detected");
  }
  
  Serial.println("============================\n");
}