; records_z80.asm - standard Z80 implementation of the 20-byte format
; EncodeRecord: DE=destination; source fields are the variables below.
; ValidateRecord: HL=record, returns Z valid / NZ invalid.
; CRC16: HL=buffer, BC=length, returns DE=CRC-16/CCITT-FALSE.

RECORD_SIZE EQU 20
MAGIC EQU 052h
VERSION EQU 1

PutByteDE: LD (DE),A
           INC DE
           RET

EncodeRecord:
           LD A,MAGIC
           CALL PutByteDE
           LD A,VERSION
           CALL PutByteDE
           LD A,(RecordFlags)
           CALL PutByteDE
           LD A,(RecordType)
           CALL PutByteDE
           LD HL,RecordId
           CALL PutWordLE
           LD HL,RecordValue
           CALL PutWordLE
           CALL PutWordLE
           LD HL,RecordQuantity
           CALL PutWordLE
           LD HL,RecordOwner
           CALL PutWordLE
           LD HL,RecordTimestamp
           CALL PutWordLE
           CALL PutWordLE
           RET

; Copy two bytes at HL to DE in little-endian order.
PutWordLE: LD A,(HL)
           CALL PutByteDE
           INC HL
           LD A,(HL)
           CALL PutByteDE
           INC HL
           RET

CRC16:     LD DE,0FFFFh
CRCByte:   LD A,B
           OR C
           RET Z
           LD A,(HL)
           INC HL
           XOR D
           LD D,A
           LD A,8
CRCBits:   SLA E
           RL D
           JR NC,CRCNoPoly
           LD A,D
           XOR 010h
           LD D,A
           LD A,E
           XOR 021h
           LD E,A
CRCNoPoly: DEC A
           JR NZ,CRCBits
           DEC BC
           JR CRCByte

ValidateRecord:
           LD A,(HL)
           CP MAGIC
           RET NZ
           INC HL
           LD A,(HL)
           CP VERSION
           RET NZ
           INC HL
           LD A,(HL)
           AND 0FCh
           RET NZ
           INC HL
           LD A,(HL)
           CP 1
           RET C
           CP 5
           RET NC
           XOR A
           RET

RecordFlags: DEFB 1
RecordType: DEFB 1
RecordId: DEFS 4
RecordValue: DEFS 4
RecordQuantity: DEFS 2
RecordOwner: DEFS 2
RecordTimestamp: DEFS 4
