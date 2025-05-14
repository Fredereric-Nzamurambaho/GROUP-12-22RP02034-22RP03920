<?php
require_once 'config.php';
require_once 'sms.php';

class Menu {
    private $conn;
    private $sms;
    private $sessionId;
    private $phoneNumber;
    private $text;
    private $userData = [];
    private $defaultBalance = 10000000;
    private $currentOrder = [];
 // Construct

    public function __construct($sessionId, $phoneNumber, $text) {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->sms = new SMS();
        $this->sessionId = $sessionId;
        $this->phoneNumber = $phoneNumber;
        $this->text = $text;
        $this->loadUserData();
    }

    private function loadUserData() {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE phone_number = ?");
            $stmt->execute([$this->phoneNumber]);
            $this->userData = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            $this->userData = null;
        }
    }

    public function processMenu() {
        try {
            $textArray = explode('*', $this->text);
            $userLevel = count($textArray);

            // Help menu paging logic for unregistered users
            if (!$this->userData && $textArray[0] == '2') {
                $page = 1;
                foreach (array_slice($textArray, 1) as $item) {
                    if ($item == '98') $page++;
                    if ($item == '99') $page--;
                }
                $page = max(1, min(3, $page));
                return $this->showHelp($page);
            }

            // Registered user: Check Order Status menu (option 2)
            if ($this->userData && $textArray[0] == '2') {
                $orderPage = 1;
                foreach (array_slice($textArray, 1) as $item) {
                    if ($item == '98') $orderPage++;
                    if ($item == '99') $orderPage--;
                }
                $orderPage = max(1, $orderPage);
                return $this->showOrderStatus($orderPage);
            }

            if (!$this->userData) {
                if ($textArray[0] == '1') {
                    return $this->processRegistration($textArray);
                } elseif ($textArray[0] == '2') {
                    return $this->showHelp();
                }
                return $this->showUnregisteredMenu();
            }

            switch ($userLevel) {
                case 1:
                    return $this->processMainMenu($textArray[0]);
                case 2:
                    return $this->processSubMenu($textArray[0], $textArray[1]);
                case 3:
                    return $this->processCategorySelection($textArray[0], $textArray[1], $textArray[2]);
                case 4:
                    return $this->processProductSelection($textArray[0], $textArray[1], $textArray[2], $textArray[3]);
                case 5:
                    return $this->processQuantityInput($textArray[0], $textArray[1], $textArray[2], $textArray[3], $textArray[4]);
                case 6:
                    return $this->processOrderConfirmation($textArray[0], $textArray[1], $textArray[2], $textArray[3], $textArray[4], $textArray[5]);
                case 7:
                    return $this->processPinVerification($textArray[6], $this->currentOrder);
                default:
                    return $this->showMainMenu();
            }
        } catch (Exception $e) {
            error_log("Error in processMenu: " . $e->getMessage());
            return "An error occurred. Please try again later.";
        }
    }

    private function showUnregisteredMenu() {
        $response = "Welcome to Online Ordering System\n";
        $response .= "1. Register\n";
        $response .= "2. Help";
        return $response;
    }

    private function showMainMenu() {
        $response = "Welcome " . $this->userData['full_names'] . "\n";
        $response .= "1. Place Order\n";
        $response .= "2. Check Order Status";
        return $response;
    }

    private function processMainMenu($choice) {
        switch ($choice) {
            case '1':
                return $this->showPlaceOrderMenu();
            case '2':
                return $this->showOrderStatus();
            default:
                return $this->showMainMenu();
        }
    }

    private function showPlaceOrderMenu() {
        
        // $response = "1. New Order\n";
        // $response .= "2. Saved Order\n";
        // $response .= "0. Main Menu";
        // return $response;
        $menu = "1. New Order\n";
        //$menu .= "2. Saved Order\n";
        $menu .= "0. Main Menu";
    
        return "CON\n" . $menu;
    }

    private function showCategories() {
        $response = "Select Category:\n";
        $response .= "1. Electronics devices\n";
        $response .= "2. Shoes\n";
        $response .= "3. Clothes\n";
        $response .= "00. Back\n";
        $response .= "0. Main Menu";
        return $response;
    }

    private function showProducts($categoryId, $page = 1) {
        try {
            // Validate category ID
            if (!is_numeric($categoryId) || $categoryId < 1) {
                return "Invalid category selection.\n00. Back\n0. Main Menu";
            }

            // First check if category exists
            $stmt = $this->conn->prepare("SELECT id FROM categories WHERE id = ?");
            $stmt->execute([(int)$categoryId]);
            if (!$stmt->fetch()) {
                return "Category not found.\n00. Back\n0. Main Menu";
            }

            // Calculate offset
            $offset = ((int)$page - 1) * 10;
            
            // Get products with proper error handling
            $stmt = $this->conn->prepare("
                SELECT p.*, c.name as category_name 
                FROM products p 
                JOIN categories c ON p.category_id = c.id 
                WHERE p.category_id = ? 
                ORDER BY p.id 
                LIMIT 10 OFFSET ?
            ");
            
            if (!$stmt->execute([(int)$categoryId, (int)$offset])) {
                throw new PDOException("Failed to execute product query");
            }

            $products = $stmt->fetchAll();
            
            if (empty($products)) {
                if ($page > 1) {
                    return "No more products in this category.\n00. Back\n0. Main Menu";
                }
                return "No products found in this category.\n00. Back\n0. Main Menu";
            }

            // Build response
            $response = "Select Product:\n";
            $i = 1;
            foreach ($products as $product) {
                $response .= $i . ". " . htmlspecialchars($product['name']) . " - " . 
                            number_format($product['price'], 2) . "\n";
                $i++;
            }

            // Add navigation options
            $response .= "98. Next Products\n";
            $response .= "00. Back\n";
            $response .= "0. Main Menu";

            return $response;

        } catch (PDOException $e) {
            error_log("Database error in showProducts: " . $e->getMessage());
            return "Error loading products. Please try again.";
        } catch (Exception $e) {
            error_log("General error in showProducts: " . $e->getMessage());
            return "An error occurred. Please try again.";
        }
    }

    private function showOrderSummary($orderItems) {
        try {
            $grouped = [];
            // Group items by product name
            foreach ($orderItems as $item) {
                $name = $item['product_name'];
                if (!isset($grouped[$name])) {
                    $grouped[$name] = [
                        'quantity' => 0,
                        'total_amount' => 0,
                    ];
                }
                $grouped[$name]['quantity'] += $item['quantity'];
                $grouped[$name]['total_amount'] += $item['total_amount'];
            }
            $response = "Your Order Summary:\n";
            $i = 1;
            $total = 0;
            foreach ($grouped as $name => $info) {
                $response .= $i . ". " . $name . " x" . $info['quantity'] . " = " . number_format($info['total_amount'], 2) . "\n";
                $total += $info['total_amount'];
                $i++;
            }
            $response .= "Total: " . number_format($total, 2) . "\n\n";
            $response .= "1. Confirm\n";
            $response .= "2. Cancel";
            return $response;
        } catch (Exception $e) {
            error_log("Error in showOrderSummary: " . $e->getMessage());
            return "Error showing order summary. Please try again.";
        }
    }

    private function showOrderStatus($page = 1) {
        $limit = 5;
        $page = (int)$page;
        if ($page < 1) $page = 1;
        $offset = ($page - 1) * $limit;
        $stmt = $this->conn->prepare("SELECT * FROM orders WHERE user_id = ? AND status = 'confirmed' ORDER BY created_at DESC LIMIT ? OFFSET ?");
        $stmt->execute([$this->userData['id'], $limit, $offset]);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($orders)) {
            return "No orders found.\n0. Main Menu";
        }

        $response = "Order Details (Page $page):\n";
        $i = $offset + 1;
        foreach ($orders as $order) {
            $response .= $i . ". Product: " . $order['product_name'] . "\n";
            $response .= "   Quantity: " . $order['quantity'] . "\n";
            $response .= "   Total: " . number_format($order['total_amount'], 2) . "\n";
            $response .= "   Status: " . $order['status'] . "\n";
            $response .= "   Date: " . $order['created_at'] . "\n";
            $response .= "----------------------\n";
            $i++;
        }
        // Check if there are more orders for next page
        $stmt2 = $this->conn->prepare("SELECT COUNT(*) as total FROM orders WHERE user_id = ? AND status = 'confirmed'");
        $stmt2->execute([$this->userData['id']]);
        $totalOrders = $stmt2->fetch(PDO::FETCH_ASSOC)['total'];
        if ($i <= $totalOrders) {
            $response .= "98. Next Orders\n";
        }
        $response .= "0. Main Menu";
        return $response;
    }

    private function processRegistration($textArray) {
        $userLevel = count($textArray);
        
        switch ($userLevel) {
            case 1:
                return "Enter your email:";
            case 2:
                if (!filter_var($textArray[1], FILTER_VALIDATE_EMAIL)) {
                    return "Invalid email format. Please enter a valid email:";
                }
                return "Enter your full names:";
            case 3:
                return "Enter your 4-digit PIN:";
            case 4:
                if (!preg_match('/^\d{4}$/', $textArray[3])) {
                    return "PIN must be 4 digits. Please try again:";
                }
                return "Enter your address:";
            case 5:
                if ($this->registerUser($textArray[1], $textArray[2], $textArray[3], $textArray[4])) {
                    $this->loadUserData();
                    $successMsg = "Registration Successful!\nCongratulations, " . $textArray[2] . ", your account has been successfully created. Welcome aboard!";
                    // Send SMS via Africa's Talking
                    $smsResult = $this->sms->sendSMS($this->phoneNumber, $successMsg);
                    if (!$smsResult['success']) {
                        error_log('SMS sending failed: ' . ($smsResult['error'] ?? 'Unknown error'));
                    }
                    return $successMsg;
                } else {
                    return "Registration failed. Please try again later.";
                }
            default:
                return $this->showUnregisteredMenu();
        }
    }

    private function showHelp($page = 1) {
        // Only show instructions for unregistered users
        $response = "Instructions:\n";
        $response .= "1. Register using option 1\n";
        $response .= "2. Once registered, you can place orders, check order status, and view your cart.\n";
        $response .= "3. Use 0 to go back to main menu anytime.\n";
        $response .= "4. Use 00 to go back to previous menu.\n\n";
        $response .= "0. Main Menu";
        return $response;
    }

    private function processSubMenu($mainChoice, $subChoice) {
        if ($mainChoice == '1') { // Place Order
            switch ($subChoice) {
                case '1':
                    return $this->showCategories();
                case '2':
                    // Saved Order logic: check for confirmation
                    $cartItems = $this->getCartItems();
                    if (empty($cartItems)) {
                        return $this->showSavedOrder();
                    }
                    // If user selects 1 (Confirm) after Saved Order
                    $textArray = explode('*', $this->text);
                    if (end($textArray) == '1') {
                        $formattedPhone = $this->formatRwandaPhone($this->phoneNumber);
                        $smsText = "Your order has been saved successfully. Thank you for using our Ordering System. We'll contact you soon for delivery.";
                        $smsResult = $this->sms->sendSMS($formattedPhone, $smsText);
                        if (!$smsResult['success']) {
                            error_log('Saved Order SMS sending failed: ' . ($smsResult['error'] ?? 'Unknown error'));
                        }
                        return "END\nYour order has been saved successfully. Thank you for using our Ordering System. We'll contact you soon for delivery.";
                    }
                    return $this->showSavedOrder();
                case '0':
                    return $this->showMainMenu();
                default:
                    return $this->showPlaceOrderMenu();
            }
        }
        return $this->showMainMenu();
    }

    private function processCategorySelection($mainChoice, $subChoice, $categoryChoice) {
        try {
            if ($mainChoice == '1' && $subChoice == '1') {
                if ($categoryChoice == '00') {
                    return $this->showPlaceOrderMenu();
                } elseif ($categoryChoice == '0') {
                    return $this->showMainMenu();
                } else {
                    // Validate category choice
                    if (!is_numeric($categoryChoice) || $categoryChoice < 1 || $categoryChoice > 3) {
                        return "Invalid category selection.\n" . $this->showCategories();
                    }
                    return $this->showProducts($categoryChoice);
                }
            }
            return $this->showMainMenu();
        } catch (Exception $e) {
            error_log("Error in processCategorySelection: " . $e->getMessage());
            return "An error occurred. Please try again.";
        }
    }

    private function processProductSelection($mainChoice, $subChoice, $categoryChoice, $productChoice) {
        if ($mainChoice == '1' && $subChoice == '1') {
            if ($productChoice == '00') {
                return $this->showCategories();
            } elseif ($productChoice == '0') {
                return $this->showMainMenu();
            } elseif ($productChoice == '98') {
                return $this->showProducts($categoryChoice, 2);
            } else {
                return "Enter quantity for selected product:";
            }
        }
        return $this->showMainMenu();
    }

    private function addToCart($product, $quantity) {
        $stmt = $this->conn->prepare("INSERT INTO cart (user_id, product_id, product_name, quantity, price, total_amount) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $this->userData['id'],
            $product['id'],
            $product['name'],
            $quantity,
            $product['price'],
            $product['price'] * $quantity
        ]);
    }

    private function getCartItems() {
        $stmt = $this->conn->prepare("SELECT * FROM cart WHERE user_id = ?");
        $stmt->execute([$this->userData['id']]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function clearCart() {
        $stmt = $this->conn->prepare("DELETE FROM cart WHERE user_id = ?");
        $stmt->execute([$this->userData['id']]);
    }

    private function processQuantityInput($mainChoice, $subChoice, $categoryChoice, $productChoice, $quantity) {
        try {
            if ($mainChoice == '1' && $subChoice == '1') {
                if (!is_numeric($quantity) || $quantity < 1) {
                    return "Invalid quantity. Please enter a valid number:";
                }
                $stmt = $this->conn->prepare("SELECT * FROM products WHERE category_id = ? LIMIT 1 OFFSET ?");
                $stmt->execute([(int)$categoryChoice, (int)($productChoice - 1)]);
                $product = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($product) {
                    $this->addToCart($product, (int)$quantity);
                    return "Product added to cart!\n1. Place Order (with product selected)\n2. Add another product from same category\n0. Main Menu";
                } else {
                    return "Product not found. Please try again.";
                }
            }
            return $this->showMainMenu();
        } catch (PDOException $e) {
            error_log("Database error in processQuantityInput: " . $e->getMessage());
            return "Debug error: " . $e->getMessage();
        }
    }

    private function processOrderConfirmation($mainChoice, $subChoice, $categoryChoice, $productChoice, $quantity, $confirmation) {
        try {
            if ($mainChoice == '1' && $subChoice == '1') {
                $cartItems = $this->getCartItems();
                if (empty($cartItems)) {
                    return "Your cart is empty.\n0. Main Menu";
                }
                if ($confirmation == '1') {
                    // Show PIN prompt only after user confirms
                    return "Enter your PIN to confirm:";
                } elseif ($confirmation == '2') {
                    // Cancel order and exit
                    $this->clearCart();
                    return "Order cancelled.\n0. Main Menu";
                } else {
                    // Show order summary and ask to choose an option (no PIN prompt here)
                    return $this->showOrderSummary($cartItems);
                }
            }
            return $this->showMainMenu();
        } catch (Exception $e) {
            error_log("Error in processOrderConfirmation: " . $e->getMessage());
            return "Debug error: " . $e->getMessage();
        }
    }

    private function processPinVerification($pin, $orderItems = null) {
        try {
            if (!$this->verifyPin($pin)) {
                return "Invalid PIN. Please try again:";
            }
            $cartItems = $this->getCartItems();
            if (empty($cartItems)) {
                return "Your cart is empty.\n0. Main Menu";
            }
            $result = $this->processOrder($cartItems);
            if ($result === true) {
                $orderDetails = "";
                $total = 0;
                foreach ($cartItems as $item) {
                    $orderDetails .= "- " . $item['product_name'] . " x" . $item['quantity'] . " = " . number_format($item['total_amount'], 2) . "\n";
                    $total += $item['total_amount'];
                }
                $message = $this->sms->sendOrderConfirmation(
                    $this->userData['full_names'],
                    $orderDetails,
                    number_format($total, 2),
                    number_format($this->userData['balance'] - $total, 2),
                    $this->userData['address']
                );
                $this->sms->sendSMS($this->phoneNumber, $message);
                $this->clearCart();
                // Always return the correct success message
                return "Order placed successfully! You will receive a confirmation SMS soon. 0. Main Menu";
            } else {
                // Show the real error for debugging
                return is_string($result) ? $result : "Order processing failed. Please try again.";
            }
        } catch (Exception $e) {
            // Show the real error for debugging
            return "Debug error: " . $e->getMessage();
        }
    }

    private function showSavedOrder() {
        $stmt = $this->conn->prepare("SELECT * FROM cart WHERE user_id = ?");
        $stmt->execute([$this->userData['id']]);
        $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($cartItems)) {
            return "You have no saved orders in your cart. Add products to your cart to save an order.\n0. Main Menu";
        }

        $response = "Saved Order:\n";
        $total = 0;
        foreach ($cartItems as $item) {
            $response .= $item['product_name'] . " x" . $item['quantity'] . " = " . $item['total_amount'] . "\n";
            $total += $item['total_amount'];
        }
        $response .= "\nTotal: " . $total . "\n\n";
        $response .= "1. Confirm\n";
        $response .= "0. Main Menu";
        return "CON\n" . $response;
    }

    private function registerUser($email, $fullNames, $pin, $address) {
        try {
            $stmt = $this->conn->prepare("INSERT INTO users (phone_number, email, full_names, pin, address, balance) VALUES (?, ?, ?, ?, ?, ?)");
            return $stmt->execute([
                $this->phoneNumber,
                $email,
                $fullNames,
                password_hash($pin, PASSWORD_DEFAULT),
                $address,
                $this->defaultBalance
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    private function verifyPin($pin) {
        return password_verify($pin, $this->userData['pin']);
    }

    private function processOrder($orderItems) {
        try {
            $this->conn->beginTransaction();
            $total = 0;
            foreach ($orderItems as $item) {
                $total += $item['total_amount'];
            }
            if ($total > $this->userData['balance']) {
                throw new Exception("Insufficient balance");
            }
            // Update user balance
            $newBalance = $this->userData['balance'] - $total;
            $stmt = $this->conn->prepare("UPDATE users SET balance = ? WHERE id = ?");
            $stmt->execute([$newBalance, $this->userData['id']]);
            // Create order
            foreach ($orderItems as $item) {
                // Ensure all required fields are present
                if (!isset($item['product_id']) || !isset($item['product_name']) || !isset($item['quantity']) || !isset($item['price']) || !isset($item['total_amount'])) {
                    error_log("Missing order item fields: " . print_r($item, true));
                    throw new Exception("Order item missing required fields: " . print_r($item, true));
                }
                $stmt = $this->conn->prepare("INSERT INTO orders (user_id, product_id, product_name, quantity, price, total_amount, status) VALUES (?, ?, ?, ?, ?, ?, 'confirmed')");
                $stmt->execute([
                    $this->userData['id'],
                    $item['product_id'],
                    $item['product_name'],
                    $item['quantity'],
                    $item['price'],
                    $item['total_amount']
                ]);
            }
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Error in processOrder: " . $e->getMessage());
            // Show error to user for debugging
            return "Order error: " . $e->getMessage();
        }
    }

    // Helper to format Rwandan phone numbers to +2507XXXXXXXX
    private function formatRwandaPhone($phone) {
        $phone = preg_replace('/\D/', '', $phone);
        if (strpos($phone, '0') === 0) {
            $phone = '250' . substr($phone, 1);
        }
        if (strpos($phone, '250') !== 0) {
            $phone = '250' . $phone;
        }
        return '+' . $phone;
    }
}
?> 
