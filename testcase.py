#!/usr/bin/python

import _thread
import requests
import json

def make_request(email):
    base_url = 'http://webserver:80/'
    #login (get token)
    url = base_url + 'auth/login'
    r = requests.post(url, json={"email" : email, "password" : "1234"})
    responseLogin = r.json()
    print(responseLogin)
    #get data chart
    url = base_url + 'cart'
    rca = requests.get(url, headers={"Authorization": "Bearer " + responseLogin['accessToken']})
    responsecart = rca.json()
    
    #checkout
    cartids = []
    for cart in responsecart:
        cartids.append(cart['product_id'])
    
    url = base_url + 'checkout'
    rco = requests.post(url, json={"product" : cartids}, headers={"Authorization": "Bearer " + responseLogin['accessToken']})
    resrco = rco.text
    
def start():
    base_url = 'http://webserver:80/'
    #retrive data user
    url = base_url + 'user'
    ruser = requests.get(url)
    resUser = ruser.json()
    for user in resUser[:100]:
        _thread.start_new_thread(make_request, (user['email'], ))  
start()

