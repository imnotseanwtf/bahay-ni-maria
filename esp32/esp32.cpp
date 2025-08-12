#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>

// ===================== CONFIGURATION =====================
constexpr char WIFI_SSID[]     = "Doctora Wifi 2.4G";
constexpr char WIFI_PASSWORD[] = "pitterdoctorawifi";
constexpr char SERVER_IP[]     = "192.168.18.161";
constexpr char DEVICE_ID[]     = "esp32-01";

// PulseSensor configuration for HW-827
constexpr int PULSE_INPUT_PIN = 34;   // Analog pin for pulse sensor
constexpr int PULSE_BLINK_PIN = 2;    // Pin to blink LED on each heartbeat
constexpr int PULSE_FADE_PIN = -1;    // Pin for fading LED (not used)
constexpr int THRESHOLD = 1900;       // Lower threshold for HW-827

constexpr unsigned long UPLOAD_INTERVAL = 10000; // 10s
constexpr unsigned long SERIAL_PLOTTER_TIME = 20; // 20ms for serial plotter

const String API_URL = String(F("http://")) + SERVER_IP + F("/api/store-sensor-value");

// ===================== GLOBALS =====================
unsigned long lastUpload = 0;
unsigned long lastSerialPlotter = 0;
int currentBPM = 0;
bool fingerDetected = false;

// Simple variables for pulse detection without library
volatile int Signal = 0;                // Holds the incoming raw data
volatile int IBI = 600;                 // Interval Between Beats (ms)
volatile boolean Pulse = false;         // True when heartbeat is detected
volatile boolean QS = false;            // Quantified Self flag
volatile int rate[10];                  // Array to hold last 10 IBI values
volatile unsigned long sampleCounter = 0;
volatile unsigned long lastBeatTime = 0;
volatile int P = 1900;                  // Peak value
volatile int T = 1900;                  // Trough value
volatile int thresh = 1950;             // Threshold for beat detection
volatile int amp = 0;                   // Amplitude of pulse waveform
volatile boolean firstBeat = true;
volatile boolean secondBeat = false;
volatile int BPM = 0;

// ===================== WIFI =====================
void connectToWiFi() {
  Serial.printf("Connecting to WiFi: %s\n", WIFI_SSID);
  WiFi.begin(WIFI_SSID, WIFI_PASSWORD);

  unsigned long startAttemptTime = millis();
  while (WiFi.status() != WL_CONNECTED && millis() - startAttemptTime < 10000) {
    delay(300);
    Serial.print(".");
  }

  if (WiFi.status() == WL_CONNECTED) {
    Serial.printf("\nWiFi Connected! IP: %s\n", WiFi.localIP().toString().c_str());
  } else {
    Serial.println(F("\nWiFi failed"));
  }
}

void ensureWiFiConnected() {
  if (WiFi.status() != WL_CONNECTED) {
    connectToWiFi();
  }
}

// ===================== PULSE DETECTION ALGORITHM =====================
void getPulse() {
  Signal = analogRead(PULSE_INPUT_PIN);
  
  unsigned long currentTime = millis();
  sampleCounter += (currentTime - lastSerialPlotter);
  int N = sampleCounter - lastBeatTime;
  
  // Find peak and trough of pulse wave
  if (Signal < thresh && N > (IBI/5)*3) {
    if (Signal < T) {
      T = Signal;
    }
  }
  
  if (Signal > thresh && Signal > P) {
    P = Signal;
  }
  
  // Look for heartbeat
  if (N > 250) {
    if ((Signal > thresh) && (Pulse == false) && (N > (IBI/5)*3)) {
      Pulse = true;
      if (PULSE_BLINK_PIN >= 0) {
        digitalWrite(PULSE_BLINK_PIN, HIGH);
      }
      
      IBI = sampleCounter - lastBeatTime;
      lastBeatTime = sampleCounter;
      
      if (secondBeat) {
        secondBeat = false;
        for (int i = 0; i <= 9; i++) {
          rate[i] = IBI;
        }
      }
      
      if (firstBeat) {
        firstBeat = false;
        secondBeat = true;
        return;
      }
      
      // Keep a running total of the last 10 IBI values
      word runningTotal = 0;
      for (int i = 0; i <= 8; i++) {
        rate[i] = rate[i+1];
        runningTotal += rate[i];
      }
      
      rate[9] = IBI;
      runningTotal += rate[9];
      runningTotal /= 10;
      BPM = 60000/runningTotal;
      
      if (BPM > 30 && BPM < 200) {
        currentBPM = BPM;
        fingerDetected = true;
        QS = true;
        Serial.printf("♥ BPM: %d, IBI: %dms\n", BPM, IBI);
      }
    }
  }
  
  if (Signal < thresh && Pulse == true) {
    if (PULSE_BLINK_PIN >= 0) {
      digitalWrite(PULSE_BLINK_PIN, LOW);
    }
    Pulse = false;
    amp = P - T;
    thresh = amp/2 + T;
    P = thresh;
    T = thresh;
  }
  
  if (N > 2500) {
    thresh = 1950;  // Reset threshold
    P = 1950;       // Reset peak
    T = 1950;       // Reset trough
    lastBeatTime = sampleCounter;
    firstBeat = true;
    secondBeat = false;
    fingerDetected = false;
    currentBPM = 0;
    Serial.println("💔 No pulse detected - sensor reset");
  }
}

// Alternative simple peak detection
bool detectPulseSimple() {
  static int lastSignal = 0;
  static int peakValue = 0;
  static int valleyValue = 4095;
  static bool risingEdge = false;
  static unsigned long lastPeakTime = 0;
  
  int currentSignal = analogRead(PULSE_INPUT_PIN);
  
  // Update peak and valley
  if (currentSignal > peakValue) peakValue = currentSignal;
  if (currentSignal < valleyValue) valleyValue = currentSignal;
  
  // Reset peak/valley every 3 seconds
  static unsigned long lastReset = 0;
  if (millis() - lastReset > 3000) {
    peakValue = currentSignal;
    valleyValue = currentSignal;
    lastReset = millis();
  }
  
  int amplitude = peakValue - valleyValue;
  fingerDetected = (amplitude > 50); // Minimum amplitude for finger detection
  
  if (!fingerDetected) return false;
  
  int threshold = valleyValue + (amplitude * 0.7); // 70% threshold
  
  // Detect rising edge
  if (!risingEdge && currentSignal > threshold && lastSignal <= threshold) {
    risingEdge = true;
  }
  
  // Detect falling edge (heartbeat)
  if (risingEdge && currentSignal < threshold && lastSignal >= threshold) {
    risingEdge = false;
    unsigned long currentTime = millis();
    
    if (lastPeakTime > 0) {
      unsigned long interval = currentTime - lastPeakTime;
      if (interval > 300 && interval < 2000) { // Valid heart rate range
        currentBPM = 60000 / interval;
        lastPeakTime = currentTime;
        
        if (PULSE_BLINK_PIN >= 0) {
          digitalWrite(PULSE_BLINK_PIN, HIGH);
          delay(50);
          digitalWrite(PULSE_BLINK_PIN, LOW);
        }
        
        Serial.printf("💓 Simple Beat! BPM: %d, Amp: %d, Thresh: %d\n", 
                     currentBPM, amplitude, threshold);
        return true;
      }
    } else {
      lastPeakTime = currentTime;
    }
  }
  
  lastSignal = currentSignal;
  return false;
}

// ===================== DATA UPLOAD =====================
void sendBpmToServer() {
  if (!fingerDetected || currentBPM < 30 || currentBPM > 200) {
    Serial.printf("⏩ Skip upload - Finger: %s, BPM: %d\n", 
                  fingerDetected ? "YES" : "NO", currentBPM);
    return;
  }

  HTTPClient http;
  http.begin(API_URL);
  http.addHeader(F("Content-Type"), F("application/json"));
  http.setTimeout(5000);

  StaticJsonDocument<200> doc;
  doc["bpm"] = currentBPM;
  doc["device_identifier"] = DEVICE_ID;
  doc["sensor_type"] = "HW-827-ESP32";
  doc["finger_detected"] = fingerDetected;
  doc["timestamp"] = millis();

  String payload;
  serializeJson(doc, payload);

  int httpCode = http.POST(payload);
  
  if (httpCode > 0) {
    Serial.printf("📤 Uploaded BPM: %d [%d]\n", currentBPM, httpCode);
  } else {
    Serial.printf("❌ Upload error: %s\n", http.errorToString(httpCode).c_str());
  }
  
  http.end();
}

// ===================== SETUP =====================
void setup() {
  Serial.begin(9600);
  delay(1000);
  
  // Setup pins
  pinMode(PULSE_INPUT_PIN, INPUT);
  if (PULSE_BLINK_PIN >= 0) {
    pinMode(PULSE_BLINK_PIN, OUTPUT);
    digitalWrite(PULSE_BLINK_PIN, LOW);
  }

  Serial.println(F("ESP32 HW-827 Pulse Sensor"));
  Serial.println(F("========================="));
  Serial.println(F("Using dual detection algorithms"));
  
  connectToWiFi();
  
  // Initialize variables
  lastSerialPlotter = millis();
  
  Serial.println(F("\n✅ Ready! Place finger on sensor"));
  Serial.println(F("💡 Try different finger positions and pressures"));
  
  // Test LED
  if (PULSE_BLINK_PIN >= 0) {
    digitalWrite(PULSE_BLINK_PIN, HIGH);
    delay(500);
    digitalWrite(PULSE_BLINK_PIN, LOW);
  }
}

// ===================== MAIN LOOP =====================
void loop() {
  // Try both detection methods
  getPulse(); // Advanced pulse detection
  
  // Also try simple detection as backup
  static unsigned long lastSimpleCheck = 0;
  if (millis() - lastSimpleCheck > 50) {
    detectPulseSimple();
    lastSimpleCheck = millis();
  }
  
  // WiFi maintenance
  static unsigned long lastWiFiCheck = 0;
  if (millis() - lastWiFiCheck > 30000) {
    ensureWiFiConnected();
    lastWiFiCheck = millis();
  }
  
  // Upload data
  if (millis() - lastUpload >= UPLOAD_INTERVAL) {
    sendBpmToServer();
    lastUpload = millis();
  }
  
  // Serial output for monitoring and plotter
  if (millis() - lastSerialPlotter >= SERIAL_PLOTTER_TIME) {
    int signal = analogRead(PULSE_INPUT_PIN);
    
    // For Arduino Serial Plotter
    Serial.printf("%d,%d,%d\n", signal, thresh, currentBPM * 10);
    
    // Human readable every second
    static unsigned long lastHumanOutput = 0;
    if (millis() - lastHumanOutput > 1000) {
      Serial.printf("📊 Signal: %d | Threshold: %d | BPM: %d | Finger: %s | Amp: %d\n", 
                    signal, thresh, currentBPM, fingerDetected ? "YES" : "NO", amp);
      lastHumanOutput = millis();
    }
    
    lastSerialPlotter = millis();
  }
  
  // Reset QS flag
  if (QS) {
    QS = false;
  }
  
  delay(2); // Small delay for stability
}