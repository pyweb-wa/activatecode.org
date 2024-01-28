import requests,time,json
import threading
import concurrent.futures
api_key = "ff984926bf26a41b536475fc8f782b20de76254c1e2cdc9d54de0b03e44d9f27"
url = f"https://sms.goonline.company/backend/out_interface.php?api_key={api_key}&action="
appcode = "wa"
country = "lb"



# def get_code(req_id):
#     start_time = time.time()
#     while time.time() - start_time < 60:
#         # Add the code that you want to run for one minute here
#         print("Function is running!")

# def run_function_for_one_minute():
#     thread = threading.Thread(target=function_to_run)
#     thread.start()
#     thread.join()

def get_Code(req_id):
    global url
    url_c = f"{url}getcode&id={req_id}"
    
    try:
        resp = requests.get(url_c)
        print(resp.text)
        res = json.loads(resp.text)
        if "Result" in res.keys():
            if "Code" in str(res["Result"]):
                print(res)
    except Exception as e:
        print(e)
     

def get_number():
    global url,appcode,country
    url_n = f"{url}getnumber&appcode={appcode}&country={country}"
    try:
        resp = requests.get(url_n)
        print(resp.text)
        res = json.loads(resp.text)
        if "Result" in res.keys():
            if "id" in res["Result"].keys():
                req_id = res["Result"]["id"]
                get_Code(req_id)
    except Exception as e:
        print(e)

get_number()

def looper():
    while True:
        get_Code(1)


t1 = threading.Thread(target=looper)
t1.start()
t2 = threading.Thread(target=looper)
t2.start()


with concurrent.futures.ThreadPoolExecutor(max_workers=30) as executor:
    # Submit 5 tasks to the executor
    tasks = [executor.submit(get_number,) for n in range(800)]

    # Wait for all tasks to finish
    concurrent.futures.wait(tasks)




