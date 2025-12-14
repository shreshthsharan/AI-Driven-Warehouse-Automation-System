#include <SPI.h>
#include <MFRC522.h>

#define SS_PIN D2   // SDA (GPIO4)
#define RST_PIN D1  // RST (GPIO5)

MFRC522 mfrc522(SS_PIN, RST_PIN);  // Create MFRC522 instance

void setup() {
  Serial.begin(9600);       // UART to ESP32
  SPI.begin();              // Init SPI bus
  mfrc522.PCD_Init();       // Init MFRC522
  delay(1000);
  Serial.println("DISPATCH READY");
}

void loop() {
  if (!mfrc522.PICC_IsNewCardPresent() || !mfrc522.PICC_ReadCardSerial()) return;

  String uid = "";
  for (byte i = 0; i < mfrc522.uid.size; i++) {
    if (mfrc522.uid.uidByte[i] < 0x10) uid += "0";
    uid += String(mfrc522.uid.uidByte[i], HEX);
  }
  uid.toUpperCase();

  Serial.println("DISPATCHED:" + uid);  // Format: DISPATCHED:<UID>

  mfrc522.PICC_HaltA();
  mfrc522.PCD_StopCrypto1();

  delay(3000);  // prevent multiple scans
}
