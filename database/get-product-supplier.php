<?php

include('connection.php');

$product_id = $_GET['product_id'];

$stmt = $conn->prepare("SELECT DISTINCT supplier.id, supplier.supplier_name FROM supplier JOIN productsupplier ON productsupplier.supplier = supplier.id WHERE productsupplier.product = ? ");

$stmt->execute([$product_id]);

$suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($suppliers as $supplier) {
    ?>

    <div class="row" style="margin-top:25px">

        <div>
            <p class="supplierName"><?= $supplier['supplier_name'] ?></p>
        </div>

        <div>

            <!-- Hidden fields added -->
            <input type="hidden" name="supplier_id[]" value="<?= $supplier['id'] ?>">
            <input type="hidden" name="product_id[]" value="<?= $product_id ?>">

            <label>Quantity:</label>
            <input type="number" class="appFormInput" name="quantity[]" placeholder="Enter Quantity">

        </div>

    </div>

    <?php
}
?>