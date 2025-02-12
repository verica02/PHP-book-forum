<?php

ob_start(); // Output buffering starts

session_start();
$pageTitle = 'Items';
if (isset($_SESSION['Username'])) {

    include 'init.php';

    $do = isset($_GET['do']) ? $_GET['do'] : 'Manage';

    // Manage Items Page
    if ($do == 'Manage') {
        $stmt = $con->prepare('SELECT * FROM `items`');
        $stmt->execute();
        $items = $stmt->fetchAll();

        if (!empty($items)) {
            ?>
            <h1 class="text-center">Manage Items</h1>
            <div class="container">
                <div class="table-responsive">
                    <table class="main-table text-center table table-bordered">
                        <tr>
                            <td>ID</td>
                            <td>Name</td>
                            <td>Description</td>
                            <td>Author</td>
                            <td>Publisher</td>
                            <td>Category</td>
                            <td>Username</td>
                            <td>Image</td>
                            <td>Control</td>
                        </tr>
                        <?php
                            foreach ($items as $item) {
                                echo '<tr>';
                                    echo '<td>' . $item['Item_ID'] . '</td>';
                                    echo '<td>' . $item['Name'] . '</td>';
                                    echo '<td>' . $item['Description'] . '</td>';
                                    echo '<td>' . $item['Author'] . '</td>';
                                    echo '<td>' . $item['Publisher_Made'] . '</td>';
                                    echo '<td>' . $item['Cat_ID'] . '</td>';
                                    echo '<td>' . $item['Member_ID'] . '</td>';
                                    echo '<td>';
                                        if (!empty($item['Image'])) {
                                            echo '<img src="../uploads/' . $item['Image'] . '" style="width: 100px; height: auto;">';
                                        } else {
                                            echo 'No Image';
                                        }
                                    echo '</td>';
                                    echo '<td>
                                        <a href="items.php?do=Edit&itemid='   . $item['Item_ID'] . '"class="btn btn-block btn-success       "><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</a>   <!-- Used the btn-block CSS class to solve the design problem of the Approve Button not being aligned with the Edit and Delete buttons on the same line (takeing a line break/newline) -->
                                        <a href="items.php?do=Delete&itemid=' . $item['Item_ID'] . '"class="btn btn-block btn-danger confirm"><i class="fa fa-times"           aria-hidden="true"></i> Delete</a> <!-- Used the btn-block CSS class to solve the design problem of the Approve Button not being aligned with the Edit and Delete buttons on the same line (takeing a line break/newline) --> ';
                                        if ($item['Approve'] == 0) { // means that the user request hasn't been appproved yet, so show the Approve Button to Admin to make Approve = 1
                                            echo '
                                                <a href="items.php?do=Approve&itemid=' . $item['Item_ID'] . '"class="btn btn-block btn-info activate"><i class="fa fa-check" aria-hidden="true"></i> Approve</a> <!-- Creating a line break/newline after the echo to be just like the previous two buttons (Edit and Delete Buttons) having line breaks/newlines between them, to avoid that the Approve Button becoming closely adhered to the Delete Button (no margin between them!). I used "View Page Source" to solve this problem arising because of using the if condition with the Approve Button, which leads to creating the Activate Button <a> HTML element without a line break/newline between it and its preceding Delete Button HTML element! --> <!-- Used the btn-block CSS class to solve the design problem of the Approve Button not being aligned with the Edit and Delete buttons on the same line (takeing a line break/newline) -->
                                            ';
                                        }
                            echo '</td>';
                        echo '</tr>';

                            }
                        ?>
                    </table>
                </div>
                <a href="items.php?do=Add" class="btn btn-primary btn-sm"><i class="fa fa-plus" aria-hidden="true"></i> New Item</a>
            </div>
            <?php
        } else {
            echo '<div class="container">';
            echo '<div class="alert alert-danger">No Items Found</div>';
            echo '</div>';
        }

    // Add New Item Page
    } elseif ($do == 'Add') { 
        ?>
        <h1 class="text-center">Add New Item</h1>
        <div class="container">
            <form class="form-horizontal" action="?do=Insert" method="POST" enctype="multipart/form-data">
                <!-- Name Field -->
                <div class="form-group form-group-lg">
                    <label class="col-sm-2 control-label">Name:</label>
                    <div class="col-sm-10 col-md-6">
                        <input class="form-control" type="text" name="name" required="required" placeholder="Name of the item">
                    </div>
                </div>
                <!-- Start: Author field -->
                <div class="form-group form-group-lg">
                    <label class="col-sm-2 control-label">Author:</label>
                    <div class="col-sm-10 col-md-6">
                        <input class="form-control" type="text" name="author" required="required" placeholder="Author of the item">
                    </div>
                </div>
                <!-- End: Author field -->
                <!-- Start: Publisher field -->
                <div class="form-group form-group-lg">
                    <label class="col-sm-2 control-label">Publisher:</label>
                    <div class="col-sm-10 col-md-6">
                        <input class="form-control" type="text" name="publisher" required="required" placeholder="Publisher">
                    </div>
                </div>
                <!-- End: Publisher field -->

                <!-- Description Field -->
                <div class="form-group form-group-lg">
                    <label class="col-sm-2 control-label">Description:</label>
                    <div class="col-sm-10 col-md-6">
                        <input class="form-control" type="text" name="description" required="required" placeholder="Description of the item">
                    </div>
                </div>
                 <!-- Start: Categories field -->
                 <div class="form-group form-group-lg">
                    <label class="col-sm-2 control-label">Category:</label>
                    <div class="col-sm-10 col-md-6">
                        <select name="category">
                            <option value="0">...</option>
<?php
                            $allCats = getAllFrom('*', '`categories`', '`ID`', 'WHERE `parent` = 0', '');
                            foreach ($allCats as $cat) {
                                echo '<option value="' . $cat['ID'] . '">' . $cat['Name'] . '</option>';
                                $childCats = getAllFrom('*', '`categories`', '`ID`', "WHERE `parent` = {$cat['ID']}", ''); // child categories = subcategories
                                foreach ($childCats as $child) {
                                    echo '<option value="' . $child['ID'] . '">---- ' . $child['Name'] . '</option>';
                                }
                            }
?>
                        </select>
                    </div>
                </div>
                <!-- End: Categories field -->
                <!-- Start: Members field -->
                <div class="form-group form-group-lg">
                    <label class="col-sm-2 control-label">Member:</label>
                    <div class="col-sm-10 col-md-6">
                        <select name="member">
                            <option value="0">...</option>
<?php
                            $allMembers = getAllFrom('*', '`users`', '`UserID`', '', '');
                            foreach ($allMembers as $user) {
                                echo '<option value="' . $user['UserID'] . '">' . $user['Username'] . '</option>';
                            }
?>
                        </select>
                    </div>
                </div>
                <!-- End: Members field -->
                <!-- Start: Tags field -->
                <div class="form-group form-group-lg">
                    <label class="col-sm-2 control-label">Tags:</label>
                    <div class="col-sm-10 col-md-6">
                        <input class="form-control" type="text" name="tags" placeholder="Separate tags with commas (,)">
                    </div>
                </div>
                <!-- End: Tags field -->
                <!-- Image Field -->
                <div class="form-group form-group-lg">
                    <label class="col-sm-2 control-label">Image:</label>
                    <div class="col-sm-10 col-md-6">
                        <input type="file" name="image" class="form-control">
                    </div>
                </div>
                <!-- Start: Submit field -->
                <div class="form-group form-group-lg">
                     <div class="col-sm-offset-2 col-sm-10">
                        <input class="btn btn-primary btn-sm" type="submit" value="Add Item">
                     </div>
                </div>
                <!-- End: Submit field -->
            </form>
        </div>


        <?php

    // Insert Item Logic
    } elseif ($do == 'Insert') { 
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Get form data
            $name        = $_POST['name'];
            $desc        = $_POST['description'];
            
            $author   = $_POST['author'];
            $publisher = $_POST['publisher'];
            
            $member  = $_POST['member'];
            $cat     = $_POST['category'];
            $tags    = $_POST['tags'];
            $image    = $_FILES['image'];


            // Form Validation
            $formErrors = array(); // Empty array

            if (empty($name)) {
                $formErrors[] = 'Name can\'t be <strong>Empty</strong>';
            }
            if (empty($desc)) {
                $formErrors[] = 'Description can\'t be <strong>Empty</strong>';
            }
            if (empty($author)) {
                $formErrors[] = 'Author can\'t be <strong>Empty</strong>';
            }
            if (empty($publisher)) {
                $formErrors[] = 'Publisher can\'t be <strong>Empty</strong>';
            }
           
            if ($member == 0) {
                $formErrors[] = 'You must choose a <strong>Member</strong>';
            }
            if ($cat == 0) {
                $formErrors[] = 'You must choose a <strong>Category</strong>';
            }


            // Image validation
            $imageName = '';
            if ($image['error'] == 0) {
                $imageName = $image['name'];
                $imageTmp  = $image['tmp_name'];
                $imageSize = $image['size'];
                $imageType = $image['type'];
                
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (!in_array($imageType, $allowedTypes)) {
                    $formErrors[] = 'Invalid image type. Only JPG, PNG, and GIF are allowed.';
                }

                if ($imageSize > 100 * 1024 * 1024) {
                    $formErrors[] = 'Image size must be less than 100MB.';
                }

                $imageExt = pathinfo($imageName, PATHINFO_EXTENSION);
                $imageFinalName = uniqid('', true) . '.' . $imageExt;
                move_uploaded_file($imageTmp, '../uploads/images/' . $imageFinalName);
            }

            // Insert into the database
            if (empty($formErrors)) {
                $stmt = $con->prepare('INSERT INTO `items` (`Name`, `Description`, `Author`, `Publisher_Made`, `Cat_ID`, `Member_ID`, `tags`, `Image`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
                $stmt->execute(array($name, $desc, $author, $publisher, $cat, $member, $tags, $imageName));
                $theMsg = '<div class="alert alert-success">' . $stmt->rowCount() . ' Record Added Successfully.</div><br>';
                redirectHome($theMsg, 'back');
            } else {
                foreach ($formErrors as $error) {
                    echo '<div class="alert alert-danger">' . $error . '</div>';
                }
            }
        } else {
            $theMsg = '<div class="alert alert-danger">Sorry, You can\'t browse this page directly by copy paste in the address bar, you must come through a POST HTTP Request.</div><br>';
            redirectHome($theMsg);
        }

    // Edit Item Page
    } elseif ($do == 'Edit') {
        $itemid = isset($_GET['itemid']) && is_numeric($_GET['itemid']) ? intval($_GET['itemid']) : 0;
        $stmt = $con->prepare('SELECT * FROM `items` WHERE `Item_ID` = ?');
        $stmt->execute(array($itemid));
        $item = $stmt->fetch();

        if ($stmt->rowCount() > 0) {
            ?>
            <h1 class="text-center">Edit Item</h1>
            <div class="container">
                <form class="form-horizontal" action="?do=Update" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="itemid" value="<?php echo $itemid ?>">
                    <!-- Name Field -->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Name:</label>
                        <div class="col-sm-10 col-md-6">
                            <input class="form-control" type="text" name="name" required="required" value="<?php echo $item['Name'] ?>">
                        </div>
                    </div>
                    <!-- Description Field -->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Description:</label>
                        <div class="col-sm-10 col-md-6">
                            <input class="form-control" type="text" name="description" required="required" value="<?php echo $item['Description'] ?>">
                        </div>
                    </div>
                    <!-- Start: Author field -->
                <div class="form-group form-group-lg">
                    <label class="col-sm-2 control-label">Author:</label>
                    <div class="col-sm-10 col-md-6">
                        <input class="form-control" type="text" name="author" required="required" placeholder="Author of the item" value="<?php echo $item['Author'] ?>">
                    </div>
                </div>
                <!-- End: Author field -->
                <!-- Start: Publisher field -->
                <div class="form-group form-group-lg">
                    <label class="col-sm-2 control-label">Publisher:</label>
                    <div class="col-sm-10 col-md-6">
                        <input class="form-control" type="text" name="publisher" required="required" placeholder="Publisher" value="<?php echo $item['Publisher_Made'] ?>">
                    </div>
                </div>
                <!-- End: Publisher field -->
<!-- Start: Categories field -->
<div class="form-group form-group-lg">
                    <label class="col-sm-2 control-label">Category:</label>
                    <div class="col-sm-10 col-md-6">
                        <select name="category">
<?php
                            $stmt2 = $con->prepare('SELECT * FROM `categories`');
                            $stmt2->execute();
                            $cats = $stmt2->fetchAll();
                            foreach ($cats as $cat) {
                                echo '<option value="' . $cat['ID'] . '"'; if ($item['Cat_ID'] == $cat['ID']) {echo 'Selected';} echo '>' . $cat['Name'] . '</option>';
                            }
?>
                        </select>
                    </div>
                </div>
                <!-- End: Categories field -->
                <!-- Start: Members field -->
                <div class="form-group form-group-lg">
                    <label class="col-sm-2 control-label">Member:</label>
                    <div class="col-sm-10 col-md-6">
                        <select name="member">
<?php
                            $stmt = $con->prepare('SELECT * FROM `users`');
                            $stmt->execute();
                            $users = $stmt->fetchAll();
                            foreach ($users as $user) {
                                echo '<option value="' . $user['UserID'] . '"'; if ($item['Member_ID'] == $user['UserID']) {echo 'Selected';} echo '>' . $user['Username'] . '</option>';
                            }
?>
                        </select>
                    </div>
                </div>
                <!-- End: Members field -->
                <!-- Start: Tags field -->
                <div class="form-group form-group-lg">
                    <label class="col-sm-2 control-label">Tags:</label>
                    <div class="col-sm-10 col-md-6">
                        <input class="form-control" type="text" name="tags" placeholder="Separate tags with commas (,)" value="<?php echo $item['tags'] ?>">
                    </div>
                </div>
                <!-- End: Tags field -->

                    <!-- Image Field -->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Image:</label>
                        <div class="col-sm-10 col-md-6">
                            <input type="file" name="image" class="form-control">
                            <?php
                                if (!empty($item['Image'])) {
                                    echo '<img src="../uploads/' . $item['Image'] . '" style="width: 150px; height: auto; margin-top: 10px;">';
                                }
                            ?>
                        </div>
                    </div>
                    <!-- Other Fields -->
                    <!-- ... -->

                    <!-- Submit Button -->
                    <div class="form-group form-group-lg">
                        <div class="col-sm-offset-2 col-sm-10">
                            <input class="btn btn-primary btn-sm" type="submit" value="Save Changes">
                        </div>
                    </div>
                </form>

                <?php
            $stmt = $con->prepare('SELECT `comments`.*, `users`.`Username` AS My_user_name FROM `comments`
                                    INNER JOIN `users` ON `users`.`UserID`  = `comments`.`user_id`
                                    WHERE `item_id` =?
            ');

            // Executing the statement
            $stmt->execute(array($itemid));

            // Retrieving/Fetching all the rows and assigning them to a variable
            $rows = $stmt->fetchAll();
            // echo '<pre>', var_dump($rows), '</pre>';
            // echo '<pre>', print_r($rows), '</pre>';

            if (!empty($rows)) {
?>
            <h1 class="text-center">Manage [<?php echo $item['Name'] ?>] Comments</h1>
                <div class="table-responsive">
                    <table class="main-table text-center table table-bordered">
                        <tr>
                            <td>Comment</td>
                            <td>Username</td>
                            <td>Added Date</td>
                            <td>Control</td>
                        </tr>
<?php
                        foreach ($rows as $row) {
                            echo '<tr>';
                                echo '<td>' . $row['comment'] . '</td>';
                                echo '<td>' . $row['My_user_name'] . '</td>';   // from the last SQL query (AS ....)
                                echo '<td>' . $row['comment_date'] . '</td>';
                                echo '<td>
                                            <a href="comments.php?do=Edit&comid='   . $row['c_id'] . '"class="btn btn-success       "><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</a>
                                            <a href="comments.php?do=Delete&comid=' . $row['c_id'] . '"class="btn btn-danger confirm"><i class="fa fa-times"           aria-hidden="true"></i> Delete</a>';
                                            
                                echo '</td>';
                            echo '</tr>';
                        }
?>

                    </table>
                </div>
<?php           
                } 
?>
            </div>
        </div>
<?php       
        // If there's no such $userid, show Error Message
        } else {
            echo '<div class="container">';
            $theMsg = '<div class="alert alert-danger">This ID does not exist.</div><br>';
            redirectHome($theMsg);
            echo '</div>';
        }

    // Update Item Logic
    } elseif ($do == 'Update') {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id          = $_POST['itemid'];
            $name        = $_POST['name'];
            $desc        = $_POST['description'];
                $author   = $_POST['author'];
                $publisher = $_POST['publisher'];
                
                $member  = $_POST['member'];
                $cat     = $_POST['category'];
                $tags     = $_POST['tags'];

            $image       = $_FILES['image'];
            $formErrors  = array();
            
            if (empty($name)) {
                $formErrors[] = 'Name can\'t be <strong>Empty</strong>';
            }
            if (empty($desc)) {
                $formErrors[] = 'Description can\'t be <strong>Empty</strong>';
            }

            if (empty($author)) {
                $formErrors[] = 'Author can\'t be <strong>Empty</strong>';
            }

            if (empty($publisher)) {
                $formErrors[] = 'Publisher can\'t be <strong>Empty</strong>';
            }

         

            if ($member == 0) {
                $formErrors[] = 'You must choose a <strong>Member</strong>';
            }

            if ($cat == 0) {
                $formErrors[] = 'You must choose a <strong>Category</strong>';
            }


            // Image upload logic
            $imageName = '';
            if ($image['error'] == 0) {
                $imageName = $image['name'];
                $imageTmp  = $image['tmp_name'];
                $imageSize = $image['size'];
                $imageType = $image['type'];

                // Validate image type and size
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (!in_array($imageType, $allowedTypes)) {
                    $formErrors[] = 'Invalid image type. Only JPG, PNG, and GIF are allowed.';
                }
                if ($imageSize > 5 * 1024 * 1024) {
                    $formErrors[] = 'Image size must be less than 5MB.';
                }
                $imageExt = pathinfo($imageName, PATHINFO_EXTENSION);
                $imageFinalName = uniqid('', true) . '.' . $imageExt;
                move_uploaded_file($imageTmp, '../uploads/' . $imageFinalName);
            }

            // Update the item in the database
            if (empty($formErrors)) {
                if (!empty($imageFinalName)) {
                    $stmt = $con->prepare('UPDATE `items` SET `Name` = ?, `Description` = ?, `Author` = ? , `Publisher_Made` = ?, `Cat_ID` = ?, `Member_ID` = ?, `tags` = ?, `Image` = ? WHERE `Item_ID` = ?');
                    $stmt->execute(array($name, $desc, $author, $publisher, $cat, $member, $tags, $imageFinalName, $id));
                } else {
                    $stmt = $con->prepare('UPDATE `items` SET `Name` = ?, `Description` = ?, `Author` = ? , `Publisher_Made` = ?, `Cat_ID` = ?, `Member_ID` = ?, `tags` = ? WHERE `Item_ID` = ?');
                    $stmt->execute(array($name, $desc, $author, $publisher, $cat, $member, $tags, $id));
                }

                $theMsg = '<div class="alert alert-success">' . $stmt->rowCount() . ' Records Updated Successfully.</div><br>';
                redirectHome($theMsg, 'back');
            } else {
                foreach ($formErrors as $error) {
                    echo '<div class="alert alert-danger">' . $error . '</div>';
                }
            }
        }
    }
    
    // Delete Item Logic
     elseif ($do == 'Delete') {
    echo '<h1 class="text-center">Delete Item</h1>';
    echo '<div class="container">';

    $itemid = isset($_GET['itemid']) && is_numeric($_GET['itemid']) ? intval($_GET['itemid']) : 0;
    
    // Check if the item exists
    $check = checkItem('`Item_ID`', '`items`', $itemid);

    if ($check > 0) {
        // Get image filename before deletion
        $stmt = $con->prepare('SELECT `Image` FROM `items` WHERE `Item_ID` = ?');
        $stmt->execute(array($itemid));
        $item = $stmt->fetch();
        
        // Delete the image file from the server if it exists
        if (!empty($item['Image']) && file_exists('../uploads/images/' . $item['Image'])) {
            // Remove the file from the server
            unlink('../uploads/images/' . $item['Image']);
        }

        // Delete the item from the database
        $stmt = $con->prepare('DELETE FROM `items` WHERE `Item_ID` = :zid');
        $stmt->bindParam(':zid', $itemid);
        $stmt->execute();

        // Success message after deletion
        $theMsg = '<div class="alert alert-success">' . $stmt->rowCount() . ' Record Deleted Successfully.</div><br>';
        redirectHome($theMsg, 'back');
    } else {
        // Item not found message
        $theMsg = '<div class="alert alert-danger">This ID does not exist.</div><br>';
        redirectHome($theMsg);
    }

    echo '</div>';
} elseif ($do == 'Approve') { // GET HTTP Request coming from the light blue Approve Button in items.php
    echo '<h1 class="text-center">Approve Item</h1>';
        echo '<div class="container">';
            // Checking if itemid GET Request is numeric only and getting its integer value
            $itemid = isset($_GET['itemid']) && is_numeric($_GET['itemid']) ? intval($_GET['itemid']) : 0;
            
            // Checking if the item exists in the database
            $check = checkItem('`Item_ID`', '`items`', $itemid);
            
            // If there's such a $userid, show the form
            if ($check > 0) { // You can add this for more security to prevent Admin to change id from address bar and edit the user data from address bar:  if ($count > 0 && $_SESSION['Username'] == $row['Username']) {
                // echo $row['UserID'] . ' ' . $row['Username'] . ' ' . $row['Password'] . ' ' . $row['FullName'] . ' ' . $row['Email'] . '<br>';
                // echo 'Good this is the form';
                $stmt = $con->prepare('UPDATE `items` SET `Approve` = 1 WHERE `Item_ID` = ?');
                $stmt->execute(array($itemid));

                $theMsg = '<div class="alert alert-success">' . $stmt->rowCount() . ' Record Approved Successfully.</div><br>';
                redirectHome($theMsg, 'back');
            } else {
                $theMsg ='<div class="alert alert-danger">This ID does Not exist (from Delete Page)</div><br>';
                redirectHome($theMsg);
            }
        echo '</div>';
}

// Include the footer.php
include $tpl . 'footer.php';

}else {
    header('Location: index.php');
    exit();
}

ob_end_flush();
?>
