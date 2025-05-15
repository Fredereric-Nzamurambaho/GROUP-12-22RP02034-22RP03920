📱 USSD-Based Online Ordering System Documentation
⚡ Seamless Mobile Ordering Without Internet
Powered by: PHP, Africa’s Talking, and MySQL

📌 STICKY OVERVIEW
👤 Unregistered Users:
Menu Options:

1. Register: Email, Full Names, PIN, Address

2. Help: How to use the system

🏦 Default Wallet Balance: 10,000,000

👨‍💼 Registered Users:
Menu Options:

1. Place Order → Choose Product, Quantity, Confirm

2. Check Order Status → View Confirmed Orders

🛍️ Order Process Flow:
Select Category

Choose Product

Enter Quantity

Confirm or Add More

Enter 4-digit PIN

Receive Confirmation (USSD + SMS)

🔐 PIN Security:
PIN required to confirm orders

Invalid PIN triggers retry

Only confirmed orders appear in the status

📲 USSD Navigation Keys
Key	Action
0	Return to Main Menu
00	Back to Previous Menu
98	Next Page (Products/Orders)

🧾 FULL DOCUMENTATION
🔹 1. UNREGISTERED USER MENU
When USSD code is dialed for the first time:

markdown

Welcome to Online Ordering System  
1. Register  
2. Help
1.1 Register
Prompted inputs:

Email

Full Names

PIN (4-digit)

Address

✅ On submission: Balance = 10,000,0000

1.2 Help

Instructions:
1. Register using option 1
2. Once registered, you can place orders, check order status, and view your cart
3. Use 0 to return to Main Menu
4. Use 00 to return to Previous Menu
🔹 2. REGISTERED USER MENU
Post registration or return visit:


Welcome [UserName]  
1. Place Order  
2. Check Order Status

2.1 Place Order

1. New Order  
2. Saved Order  
0. Main Menu

2.1.1 New Order
Select Product Category:
1. Electronics devices  
2. Shoes  
3. Clothes  
00. Back  
0. Main Menu
Example: User selects 1 (Electronics devices)

Product List Displayed:

1. LENOVO-V-XX—YY  
2. DELL-PQ-88-ZZ  
3. SAMSUNG-S10-XX  
98. Next Products  
00. Back  
0. Main Menu
Example: User selects 1 (LENOVO-V-XX—YY)

Prompt:


Enter quantity for LENOVO-V-XX—YY:
Post Quantity Selection:



1. Place Order  
2. Add another product from same category  
0. Main Menu
Order Summary Example:

mathematica

Your Order Summary:  
1. LENOVO-V-XX—YY x2 = 300,000  
Total: 300,000  

1. Confirm  
2. Cancel
On Confirm → Prompt:


Enter your PIN:
✅ If valid:

USSD Message:


Thanks for using our Ordering system, you will receive a confirmation message later.
SMS via Africa’s Talking:


Hello [UserName],  
Your order for:  
- LENOVO-V-XX—YY x2 = 300,000  
Total Paid: 300,000  
Balance Remaining: 0  

We appreciate your trust in us.  
Delivery will be made to: [User Address]  
Thank you!
2.1.2 Saved Order
Display unconfirmed items in cart:

Saved Order:  
1. LENOVO-V-XX—YY x2 = 300,000  
Total: 300,000  

1. Confirm  
0. Main Menu
Same confirmation and PIN flow as 2.1.1

2.2 Check Order Status
Shows only confirmed orders:

Your Orders:  
1. LENOVO-V-XX—YY x2 = 300,000 - Confirmed  
2. DELL-PQ-88-ZZ x1 = 250,000 - Confirmed  

98. Next Orders  
0. Main Menu
🔁 USSD NAVIGATION KEYS
Key	Function
0	Return to Main Menu
00	Back to Previous Menu
98	View Next Page

🧮 DEFAULT VALUES
Field	Value
Balance	10,000,000
Currency	As applicable

🔐 SECURITY & VALIDATION
✅ 4-digit PIN is mandatory for confirming any order

❌ On invalid PIN: user is prompted to retry

✔️ Only confirmed orders appear in the Order Status section

❌ CANCEL FLOW
If user selects Cancel at confirmation:


Thanks for using our service.