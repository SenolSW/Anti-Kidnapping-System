import jinja2
import pdfkit
from datetime import datetime
import json
import base64
import binascii
from Crypto.Cipher import AES
from Crypto import Random
import mysql.connector
import os
import smtplib
import ssl
from email.message import EmailMessage
import keyboard
import time
import requests

while True:
    if keyboard.is_pressed('space'):
        r = requests.get('https://get.geojs.io/')
        ip_request = requests.get('https://get.geojs.io/v1/ip.json')
        ipAdd = ip_request.json()['ip']

        url = 'https://get.geojs.io/v1/ip/geo/'+ipAdd+'.json'
        geo_request = requests.get(url)
        geo_data = geo_request.json()

        latitude = geo_data['latitude']
        longitude = geo_data['longitude']

        mydb = mysql.connector.connect(
            host="localhost",
            user="root",
            password="",
            database="salvation_db"
        )

        mycurser = mydb.cursor()

        sql = "SELECT * FROM profile_table WHERE customer_id = 4 AND victim_status = 'Active'"

        mycurser.execute(sql)
        presult = mycurser.fetchall()

        for prow in presult:

            enc_profile_image   = prow[1]
            enc_profile_f_name  = prow[2]
            enc_profile_l_name  = prow[3]
            enc_profile_dob     = prow[4]
            enc_profile_gender  = prow[5]
            enc_profile_height  = prow[6]

        SQL = "SELECT * FROM emergency_table WHERE customer_id = 4"

        mycurser.execute(SQL)

        eresult = mycurser.fetchall()

        passphrase = b'78589cef696570f4f96ada9e22d16abd6f6c33d3253e49842e8017771844df99';

        def my_decrypt(data, passphrase):
            """
                 Decrypt using AES-256-CBC with iv
                'passphrase' must be in hex, generate with 'openssl rand -hex 32'
                # https://stackoverflow.com/a/54166852/11061370
            """
            try:
                unpad = lambda s : s[:-s[-1]]
                key = binascii.unhexlify(passphrase)
                encrypted = json.loads(base64.b64decode(data).decode('ascii'))
                encrypted_data = base64.b64decode(encrypted['data'])
                iv = base64.b64decode(encrypted['iv'])
                cipher = AES.new(key, AES.MODE_CBC, iv)
                decrypted = cipher.decrypt(encrypted_data)
                clean = unpad(decrypted).decode('ascii').rstrip()
            except Exception as e:
                print("Cannot decrypt datas...")
                print(e)
                exit(1)
            return clean

        profile_image = enc_profile_image
        profile_f_name = my_decrypt(enc_profile_f_name, passphrase)
        profile_l_name = my_decrypt(enc_profile_l_name, passphrase)
        profile_dob = my_decrypt(enc_profile_dob, passphrase)
        profile_gender = my_decrypt(enc_profile_gender, passphrase)
        profile_height = my_decrypt(enc_profile_height, passphrase)

        e1d_email = my_decrypt(eresult[0][1], passphrase)
        e1d_f_name = my_decrypt(eresult[0][2], passphrase)
        e1d_l_name = my_decrypt(eresult[0][3], passphrase)
        e1d_contact = my_decrypt(eresult[0][4], passphrase)
        e1d_address = my_decrypt(eresult[0][5], passphrase)

        e2d_email = my_decrypt(eresult[1][1], passphrase)
        e2d_f_name = my_decrypt(eresult[1][2], passphrase)
        e2d_l_name = my_decrypt(eresult[1][3], passphrase)
        e2d_contact = my_decrypt(eresult[1][4], passphrase)
        e2d_address = my_decrypt(eresult[1][5], passphrase)

        context = {'profile_image':profile_image, 'profile_f_name':profile_f_name, 'profile_l_name':profile_l_name, 'profile_dob':profile_dob, 'profile_gender':profile_gender, 'profile_height':profile_height, 'e1d_email':e1d_email, 'e1d_f_name':e1d_f_name, 'e1d_l_name':e1d_l_name, 'e1d_contact':e1d_contact, 'e1d_address':e1d_address, 'e2d_email':e2d_email, 'e2d_f_name':e2d_f_name, 'e2d_l_name':e2d_l_name, 'e2d_contact':e2d_contact, 'e2d_address':e2d_address, 'latitude':latitude, 'longitude':longitude}

        template_loader = jinja2.FileSystemLoader('./')
        template_environment = jinja2.Environment(loader=template_loader)

        template = template_environment.get_template("profile_template.html")
        output_text = template.render(context)

        config = pdfkit.configuration(wkhtmltopdf="C:/Program Files/wkhtmltopdf/bin/wkhtmltopdf.exe")
        pdfkit.from_string(output_text, 'victim_profile.pdf', configuration = config)

        EMAIL_ADDRESS  = 'SalvationJewellers@gmail.com'
        EMAIL_PASSWORD = 'uyizoajaamkpaglj'

        msg = EmailMessage()
        msg['Subject'] = 'Kidnapping Alert!'
        msg['From'] = EMAIL_ADDRESS
        msg['To'] = [e1d_email,e2d_email]
        Body = "Please find the attached Profile."
        msg.set_content(Body)
        context = ssl.create_default_context()

        files = ['Victim_Profile.pdf']
        for file in files:
            with open(file, 'rb') as f:
                file_data = f.read()
                file_name = f.name

            msg.add_attachment(file_data, maintype='application', subtype='octet-stream', filename=file_name)

        with smtplib.SMTP_SSL('smtp.gmail.com', 465, context=context) as smtp:
            smtp.login(EMAIL_ADDRESS, EMAIL_PASSWORD)
            smtp.send_message(msg)

        break
        #time.sleep(1)