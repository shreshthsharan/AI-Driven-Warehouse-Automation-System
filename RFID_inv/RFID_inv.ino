#include <WiFi.h>
#include <SPI.h>
#include <MFRC522.h>
#include <Wire.h>
#include <U8g2lib.h>
#include <HTTPClient.h>
#include <map>

// --- Pin Definitions ---
#define SDA_PIN 21
#define SCL_PIN 22
#define SS_PIN 5
#define RST_PIN 4
#define BUZZER_PIN 2
#define LED_PIN 15
#define SCK_PIN 18
#define MOSI_PIN 23
#define MISO_PIN 19
#define RXD2 16
#define TXD2 17

// --- WiFi Credentials ---
const char* ssid = "RedmiPrime";
const char* password = "23456787";
const char* serverURL = "http://192.168.43.31/rfid_logger.php";

// --- OLED Setup (SH1106 I2C, 128x64, 180° Rotation) ---
U8G2_SH1106_128X64_NONAME_F_HW_I2C u8g2(U8G2_R2, U8X8_PIN_NONE);

// --- RFID Setup ---
MFRC522 mfrc522_1(SS_PIN, RST_PIN);

// --- Item Data ---
struct Item {
  String name;
  String size;
  String mfgDate;
  String expDate;
};
std::map<String, Item> itemDatabase;

void setupItemDatabase() {
  itemDatabase["73F785E2"] = {"PRODUCT A", "XL", "2025-06-01", "2026-06-01"};
  itemDatabase["933497E2"] = {"PRODUCT B", "1L", "2024-12-01", "2029-12-01"};
  itemDatabase["40E7BA14"] = {"PRODUCT C", "42", "2025-01-15", "2027-01-15"};
  itemDatabase["93FF89E2"] = {"PRODUCT D", "Free", "2025-03-10", "2026-03-10"};
}

void showIdleScreen() {
  u8g2.clearBuffer();
  u8g2.setFont(u8g2_font_ncenB14_tr);
  u8g2.drawStr(0, 25, "Waiting");
  u8g2.drawStr(0, 50, "for Parcel...");
  u8g2.sendBuffer();
}

void showMessage(String line1, String line2) {
  u8g2.clearBuffer();
  u8g2.setFont(u8g2_font_6x12_tr);
  u8g2.drawStr(0, 15, line1.c_str());
  u8g2.drawStr(0, 35, line2.c_str());
  u8g2.sendBuffer();
}

void handleScannedUID(String uid, String statusLabel) {
  Item item;
  if (itemDatabase.count(uid)) {
    item = itemDatabase[uid];
  } else {
    item = {"Unknown", "-", "-", "-"};
  }

  digitalWrite(LED_PIN, HIGH);
  for (int i = 0; i < 2; i++) {
    digitalWrite(BUZZER_PIN, HIGH);
    delay(200);
    digitalWrite(BUZZER_PIN, LOW);
    delay(200);
  }
  digitalWrite(LED_PIN, LOW);

  u8g2.clearBuffer();
  u8g2.setFont(u8g2_font_ncenB14_tr);
  u8g2.drawStr(10, 15, statusLabel.c_str());

  u8g2.setFont(u8g2_font_6x12_tr);
  u8g2.drawStr(0, 30, ("Item: " + item.name).c_str());
  u8g2.drawStr(0, 40, ("Size: " + item.size).c_str());
  u8g2.drawStr(0, 50, ("MFG: " + item.mfgDate).c_str());
  u8g2.drawStr(0, 60, ("EXP: " + item.expDate).c_str());
  u8g2.sendBuffer();

  // Send to PHP
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    http.begin(serverURL);
    http.addHeader("Content-Type", "application/json");

    String payload = "{";
    payload += "\"rfid\":\"" + uid + "\",";
    payload += "\"status\":\"" + statusLabel + "\",";
    payload += "\"item\":\"" + item.name + "\",";
    payload += "\"size\":\"" + item.size + "\",";
    payload += "\"mfg\":\"" + item.mfgDate + "\",";
    payload += "\"exp\":\"" + item.expDate + "\"";
    payload += "}";

    http.POST(payload);
    http.end();
  }

  delay(4000);
  showIdleScreen();
}

void checkReader(MFRC522 &reader, String statusLabel) {
  if (!reader.PICC_IsNewCardPresent() || !reader.PICC_ReadCardSerial()) return;

  String uid = "";
  for (byte i = 0; i < reader.uid.size; i++) {
    if (reader.uid.uidByte[i] < 0x10) uid += "0";
    uid += String(reader.uid.uidByte[i], HEX);
  }
  uid.toUpperCase();

  handleScannedUID(uid, statusLabel);
  reader.PICC_HaltA();
  reader.PCD_StopCrypto1();
}

void setup() {
  Serial.begin(115200);
  Serial2.begin(9600, SERIAL_8N1, RXD2, TXD2);

  pinMode(BUZZER_PIN, OUTPUT);
  pinMode(LED_PIN, OUTPUT);
  digitalWrite(BUZZER_PIN, LOW);
  digitalWrite(LED_PIN, LOW);

  Wire.begin(SDA_PIN, SCL_PIN);
  u8g2.begin();

  // Test UART
  u8g2.clearBuffer();
  u8g2.setFont(u8g2_font_6x12_tr);
  u8g2.drawStr(0, 10, "Checking UART...");
  u8g2.sendBuffer();
  delay(500);

  bool uartConnected = false;
  long startTime = millis();
  while (millis() - startTime < 3000) {
    if (Serial2.available()) {
      String line = Serial2.readStringUntil('\n');
      if (line.indexOf("DISPATCH READY") >= 0) {
        uartConnected = true;
        break;
      }
    }
  }
  u8g2.drawStr(0, 25, uartConnected ? "UART OK" : "UART FAIL");
  u8g2.sendBuffer();
  delay(1000);

  // WiFi
  u8g2.drawStr(0, 40, "Connecting WiFi...");
  u8g2.sendBuffer();
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  u8g2.drawStr(0, 55, "WiFi Connected");
  u8g2.sendBuffer();
  delay(1500);

  SPI.begin(SCK_PIN, MISO_PIN, MOSI_PIN, SS_PIN);
  mfrc522_1.PCD_Init();
  setupItemDatabase();
  showIdleScreen();
}

void loop() {
  checkReader(mfrc522_1, "Received");

  if (Serial2.available()) {
    String input = Serial2.readStringUntil('\n');
    input.trim();
    if (input.startsWith("DISPATCHED:")) {
      String uid = input.substring(11);
      uid.toUpperCase();
      handleScannedUID(uid, "Dispatched");
    }
  }
}
