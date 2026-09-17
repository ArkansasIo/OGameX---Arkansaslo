from pathlib import Path
from openpyxl import Workbook
from openpyxl.styles import Font, PatternFill, Alignment
from openpyxl.worksheet.datavalidation import DataValidation
from openpyxl.formatting.rule import FormulaRule
from openpyxl.utils import get_column_letter

ROOT=Path(__file__).resolve().parents[1]; out=ROOT/'excel_template.xlsx'
wb=Workbook(); ws=wb.active; ws.title='Records'; ins=wb.create_sheet('Instructions')
headers=['Magic','Version','Flags','Record Type','Record ID','Value','Quantity','Owner ID','Timestamp','CRC-16','Packed Hex','Valid?']
ws.append(headers)
rows=[['=82',1,1,1,1001,2500,1,7,1726550400],['=82',1,1,2,2001,90000,12,7,1726550400],['=82',1,1,3,3001,450,320,7,1726550400]]
for row in rows: ws.append(row+['=0','=CONCAT("52",DEC2HEX(B2,2),DEC2HEX(C2,2),DEC2HEX(D2,2))','=AND(A2=82,B2=1,C2>=0,C2<=3,D2>=1,D2<=4,E2>=0,E2<=4294967295,F2>=0,F2<=4294967295,G2>=0,G2<=65535,H2>=0,H2<=65535,I2>=0,I2<=4294967295)'])
for c in range(1,13): ws.cell(1,c).font=Font(bold=True,color='FFFFFF'); ws.cell(1,c).fill=PatternFill('solid',fgColor='1F4E78'); ws.cell(1,c).alignment=Alignment(horizontal='center')
widths=[10,10,10,14,14,14,12,12,16,12,24,12]
for i,w in enumerate(widths,1): ws.column_dimensions[get_column_letter(i)].width=w
ws.freeze_panes='A2'; ws.auto_filter.ref='A1:L1001'
dv=DataValidation(type='whole',operator='between',formula1='1',formula2='4',allow_blank=False); dv.error='Record type must be 1..4'; ws.add_data_validation(dv); dv.add('D2:D1001')
ws.conditional_formatting.add('L2:L1001',FormulaRule(formula=['L2=FALSE'],fill=PatternFill('solid',fgColor='FFC7CE')))
ins.append(['8-bit Fixed Records — Excel Template']); ins.append([]); ins.append(['Enter one record per row on Records. Numeric fields are unsigned and must stay within the bounds below.']); ins.append(['Field','Meaning','Range']);
for r in [('Magic','Constant record marker','82'),('Version','Format version','1'),('Flags','Bit 0 active, bit 1 deleted','0..3'),('Record Type','1 player, 2 planet, 3 resource, 4 event','1..4'),('Record ID','Unsigned 32-bit identifier','0..4294967295'),('Value','Unsigned 32-bit value','0..4294967295'),('Quantity','Unsigned 16-bit quantity','0..65535'),('Owner ID','Unsigned 16-bit owner','0..65535'),('Timestamp','Unsigned Unix seconds','0..4294967295')]: ins.append(list(r))
ins.append([]); ins.append(['CRC algorithm','CRC-16/CCITT-FALSE over all 20 packed bytes; polynomial 0x1021, initial 0xFFFF.'])
ins.column_dimensions['A'].width=22; ins.column_dimensions['B'].width=90; ins.column_dimensions['C'].width=24
for cell in ins[1]: cell.font=Font(bold=True,size=14)
wb.save(out); print(out)
