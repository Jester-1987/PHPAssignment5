<!-- I'm piggy-backing off of last week's assignment where I had created the countries drop-down menu-->

<?php
    require_once('model/database.php');

    $customer_id = filter_input(INPUT_POST, 'customer_id', FILTER_VALIDATE_INT);

    // Checking if we're updating a customer
    if ($customer_id) {
        $query = 'SELECT * FROM customers WHERE customerID = :customer_id';
        $statement = $db->prepare($query);
        $statement->bindValue(':customer_id', $customer_id);
        $statement->execute();
        $customers = $statement->fetch();
        $statement->closeCursor();
    } else {
        // empty array for adding a new customer
        $customer = [
            'firstName' => '',
            'lastName' => '',
            'address' => '',
            'city' => '',
            'state' => '',
            'postalCode' => '',
            'country' => 'United States',
            'phone' => '',
            'emailAddress' => '',
            'password' => ''
        ];
    }

    $country_query = 'SELECT countryName FROM countries';
    $country_statement = $db->prepare($country_query);
    $country_statement->execute();
    $countries = $country_statement->fetchAll();
    $country_statement->closeCursor();

    $firstNameError = '';
    $lastNameError = '';
    $addressError = '';
    $cityError = '';
    $stateError = '';
    $postalCodeError = '';
    $passwordError = '';
    $phoneError = '';
    $emailAddressError = '';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $firstName = trim($_POST['first_name']);
        $lastName = trim($_POST['last_name']);
        $address = trim($_POST['address']);
        $city = trim($_POST['city']);
        $state = trim($_POST['state']);
        $postalCode = trim($_POST['postal_code']);
        $password = trim($_POST['password']);
        $phone = trim($_POST['phone_number']);
        $emailAddress = trim($_POST['email_address']);

        if (empty($firstName)) {
            $firstNameError = 'Required.';
        } elseif (strlen($firstName) > 51) {
            $firstNameError = 'Cannot exceed 51 characters.';
        }
        
        if (empty($lastName)) {
            $lastNameError = 'Required.';
        } elseif (strlen($lastName) > 51) {
            $lastNameError = 'Cannot exceed 51 characters.';
        }
        
        if (empty($address)) {
            $addressError = 'Required.';
        } elseif (strlen($address) > 51) {
            $addressError = 'Cannot exceed 51 characters.';
        }
        
        if (empty($city)) {
            $cityError = 'Required.';
        } elseif (strlen($city) > 51) {
            $city = 'Cannot exceed 51 characters.';
        }
        
        if (empty($state)) {
            $stateError = 'Required.';
        } elseif (strlen($state) > 51) {
            $stateError = 'Cannot exceed 51 characters.';
        }
        
        if (empty($postalCode)) {
            $postalCodeError = 'Required.';
        } elseif (strlen($state) > 21) {
            $postalCodeError = 'Cannot exceed 21 characters.';
        }
        
        if (empty($password)) {
            $passwordError = 'Required.';
        } elseif (strlen($password) < 6) {
            $passwordError = 'Too short.';
        } elseif (strlen($password) > 21) {
            $passwordError = 'Cannot exceed 21 characters.';
        }

        $phonePattern = '/^\(\d{3}\) \d{3}-\d{4}$/';
        if (empty($phone)) {
            $phoneError = 'Required.';
        } elseif (!preg_match($phonePattern, $phone)) {
            $phoneError = 'Use (999) 999-9999 format.';
        }

        if (empty($emailAddress)) {
            $emailAddressError = 'Required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailAddressError = 'Invalid email address.'
        }
    }

?>
<!DOCTYPE html>
<html>
   <head>
       <title><?php echo $customer_id ? 'Update Customer' : 'Add Customer'; ?></title>
       <link rel="stylesheet" type="text/css" href="main.css" />       
   </head>
   <body>
       <?php include("view/header.php"); ?>
       <main>
        <h2><?php echo $customer_id ? 'Update Customer' : 'Add Customer'; ?></h2>

        <form action="<?php echo $customer_id ? 'update_contact.php' : 'add_customer.php'; ?>" method="post" id="update_contact_form">
        <div id="data">

            <?php if($customer_id): ?>
                <input type="hidden" name="customer_id" value ="<?php echo $customer[customerID];?>" />
            <?php endif; ?>

            <input type="hidden" name="customer_id"
                value="<?php echo $customer['customerID']; ?>" />

            <label>First Name:</label>
            <input type="text" name="first_name"
            value="<?php echo $customer['firstName']; ?>" />
            <span style="color:red;"><?php echo $firstNameError; ?><br />

            <label>Last Name:</label>
            <input type="text" name="last_name"
            value="<?php echo $customer['lastName']; ?>" />
            <span style="color:red;"><?php echo $lastNameError; ?><br />

            <label>Address:</label>
            <input type="text" name="address"
            value="<?php echo $customer['address']; ?>" />
            <span style="color:red;"><?php echo $addressError; ?><br />

            <label>City:</label>
            <input type="text" name="city"
            value="<?php echo $customer['city']; ?>" />
            <span style="color:red;"><?php echo $cityError; ?><br />

            <label>State:</label>
            <input type="text" name="state"
            value="<?php echo $customer['state']; ?>" />
            <span style="color:red;"><?php echo $stateError; ?><br />

            <label>Postal Code:</label>
            <input type="text" name="postal_code"
            value="<?php echo $customer['postalCode']; ?>" />
            <span style="color:red;"><?php echo $postalCodeError; ?><br />

            <label>Country:</label>
            <select name="country">
                <?php foreach ($countries as $country) : ?>
                    <option value="<?php echo $country['countryName']; ?>"
                        <?php
                            // If customer has a country, select it. Otherwise, default to 'United States'
                            if (($customer['country'] == $country['countryName']) || 
                                (empty($customer['country']) && $country['countryName'] == 'United States')) {
                                echo 'selected';
                            }
                        ?>>
                        <?php echo $country['countryName']; ?>
                    </option>
                <?php endforeach; ?>
            </select><br />       

            <label>Phone Number:</label>
            <input type="text" name="phone_number"
                value="<?php echo $customer['phone']; ?>" />
                <span style="color:red;"><?php echo $phoneError; ?><br />
            
            <label>Email Address:</label>
            <input type="text" name="email_address"
            value="<?php echo $customer['emailAddress']; ?>" />
            <span style="color:red;"><?php echo $emailAddressError; ?><br />

            <label>Password:</label> 
            <input type="text" name="password"
                value="<?php echo $customer['password']; ?>" />
                <span style="color:red;"><?php echo $passwordError; ?><br /> 
        </div>

        <div id="buttons">
            <label>&nbsp;</label>
            <input type="submit" value="<?php echo $customer_id ? 'Update Customer' : 'Add Customer'; ?>" /><br />
        </div>

        </form>
        
        <p><a href="customer_manager/index.php">View Customer List</a></p>
       </main>
       <?php include("view/footer.php"); ?>
   </body>
</html>