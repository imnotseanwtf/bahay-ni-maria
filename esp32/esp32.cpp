#include <TinyGPSPlus.h>
#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>
#include "MAX30105.h"
#include "heartRate.h"

// WiFi credentials
#define WIFI_SSID "Doctora Wifi 2.4G"
#define WIFI_PASSWORD "pitterdoctorawifi"
#define API_URL "https://lightskyblue-gnat-930724.hostingersite.com/api/store-sensor-value"
#define DEVICE_ID "esp32-01"

// GPS configuration
TinyGPSPlus gps;
#define RX_PIN 16 // Connect to NEO-6M TX
#define TX_PIN 17 // Connect to NEO-6M RX
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




  iFi.status() == WL_CONNECTED) {
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
  
  //

   data
  readHeartRateData();
  
  //

     ata
  print
              s
          );

  //












           s
  stati



                   0;
  if (m
          is
                   astUpl
               ad > 3000) {
               las

             lis();
    sen

         ();
  }



        a

     0);
}

void readGPSData() {
  while (gpsSerial.available() > 0) {
    if (gps.encode(gpsSerial.read())) {
      if (gps.location.isValid()) {
        latitude = gps.location.lat();
        longitude = gps.location.lng();
        gpsValid = true;


        altitude.isValid()) {
          altitude = gps.altitude.meters();
        }


        speed.isValid()) {
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
  
  //

  a
          r
          GPS\":{");
  Seria
    rin
      valid\":");
  Seria
    rin
      Valid ? "true" : "false");
  Seria
    rin
      "lat\":");
  Seria
    rin
      itude, 6);
  Seria
    rin
      "lng\":");
  Seria
    rin
      gitude, 6);
  Seria
    rin
      "alt\":");
  Seria
    rin
      itude, 2);
  Seria
    rin
      "speed\":");
  Seria
    rin
      ed_kmh, 2);
  Seria
    rin
      ");
  
  //

  a


      a
              r
              HeartRate\":{");
  Seria
    rin
      BPM\":");
  Seria
    rin
      tsPerMinute);
  Seria
    rin
      "AvgBPM\":");
  Seria
    rin
      tAvg);
  Seria
    rin
      "Finger\":");
  Seria
    rin
      gerDetected ? "true" : "false");
  Seria
    rin
      );

  Se























      f (W

               CONNECTED) {
    Ser

                      ❌ Wi-Fi not co n
            nected. Cannot sed data.");
    retur










        heart rate data (GPS

                 onal)
  if (!fi rDete
        rM
          || beatsPerMinute > 200) {
    Seria


                   Finger: %
          , BPM:  %d \n
          , f ing erD etected ? "YES" : "NO ",  beats Pe rMinute);
    retur
                      TTP





                begi
    PI_URL);
  http.addH
    er(F("C
      t-Type"), F("application/json"));
  http.setT
    out(500
        // Creat

    ON pay

  ith both GPS and heart rate data
  StaticJso
        cument<
          doc;
  doc[
              devi
    identifier"] = DEVICE_ID;
  doc["time
    mp"] = millis();

  // Hea


  doc["bpm"
       beatsPerMinute;
  doc["avg_bpm"] = beatAvg;
  doc["finger_detected"] = fingerDetected;
  
  // GPS

  doc["g

  id"] = gpsValid;
  if (gpsValid) {
    doc["la
      e"] = latitude;
    doc["lo
      de"] = longitude;
  }

  String payload;
  serializeJson(doc, payload);

  int httpCode = http.POST(payload);

  if (httpCode > 0) {
    Serial.printf("📤 Data uploaded - BPM: %d, GPS: %s [HTTP: %d]\n", 



                  VALID" : "INVALID", httpCode);
    
    if (

    0) {

         S








                         " + response);
    }
  } else {
    Serial.prin
           Upload e
                \n", http.er ror
                               ToS tring(httpCode).c_str());
  }

  http.end();
}

void displ
    y
      e

       a

  edInfo()


          Detailed G
            fo (call this function manually if needed for debugging)
  Serial.println(
    === DETAILED SENSOR INFO ===");
  
  if (gpsValid

  al.printf("📍 GPS Location: %.6f, %.6f\n", latitude, longitude);
    Serial.printf("🏔️  Altitude: %.2f meters\n", altitude);
    Serial.printf("🚗 Speed: %.2f km/h\n", speed_kmh);
    
    if (gps.date.isV

    gps.time.isValid()) {
      Serial.printf("📅 D
    4d-%02d-%02d\n", gps.date.year(), gps.date.month(), gps.date.day());
      Serial.printf("🕐 Tim
    :%02d:%02d UTC\n", gps.time.hour(), gps.time.minute(), gps.time.second());
    }
  } else {
    Serial.print

      G
      S
   N

        ;











                          te:



                M

        BPM )\n",b







                           sPer




                  ial.prin
    a
    t Rate
      nger detected"
          ;
                }
  

i


  ==\n");
}