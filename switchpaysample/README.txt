Before you run the sample, follow the below steps:
1) Create an account on https://switchpay.in
2) Get the account approved 
3) Navigate to the APIs section and generate a token
4) Store this token safely
5) Go to Settings -> Payment Aggregrator Configs
6) Click "Add Config"
7) Select the PG that you have an account with and enter the respective keys 
8) The Label field is a placeholder for you to name your config
9) After the config is saved successfully proceed to the next step

To run the sample:
1) Get the user_uuid from APIs section of the switchpay dashboard
2) Input the user_uuid value into test.php as the value of $user_uuid
3) Input the token value (generated in step 3 above) into test.php as the value of $token
4) Set the BASE_URL value as https://switchpay.in
5) Open terminal and navigate to the folder that contains the switchpay_sample_code
6) Start a server using the command "php -S localhost:10000", this should start the server on port 10000
7) Navigate to localhost:10000 & you should be able to see the index.html page 
8) Fill up the values as necessary
9) In callback url, replace <<HOST_SERVER>> with localhost:10000
10) Click submit
11) You will be taken to the PG configured in your switchpay account
12) Post a successful txn you will be redirected to callback.php & all the necessary params will be printed on screen
