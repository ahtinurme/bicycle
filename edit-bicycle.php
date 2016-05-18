<?php
require 'config.php';
$message = false;
$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;
if ($_POST) {
    $ok = true;
    $data = [
        ':weight' => 0,
        ':price' => 0,
        ':parts' => null,
        ':name' => strip_tags(isset($_POST['name']) ? $_POST['name'] : ''),
        ':brand' => strip_tags(isset($_POST['brand']) ? $_POST['brand'] : '')
    ];
    $partArray = [];
    if (is_array($_POST['names'])) {
        foreach ($_POST['names'] as $pid => $name) {
            $newPart = [];
            if (strlen($name) > 0) {
                $newPart['name'] = strip_tags($name);
                $newPart['weight'] = isset($_POST['weights'][$pid]) && is_numeric($_POST['weights'][$pid]) ? $_POST['weights'][$pid] : 0;
                $data[':weight'] += $newPart['weight'];
                $newPart['price'] = isset($_POST['prices'][$pid]) && is_numeric($_POST['prices'][$pid]) ? $_POST['prices'][$pid] : 0;
                $data[':price'] += $newPart['price'];
                $partArray[] = $newPart;
            }
        }
        if (count($partArray) > 0) {
            $data[':parts'] = json_encode($partArray);
        }
        if ($data[':weight'] > 50) {
            $ok = false;
        }
    }
    if ($id) {
        $sql = 'UPDATE bicycles SET name = :name, weight = :weight, price = :price, parts = :parts, brand = :brand WHERE id = :id';
        $data[':id'] = $id;
    }
    else {
        $sql = 'INSERT INTO bicycles (weight, price, parts, brand, `name`) VALUES (:weight, :price, :parts, :brand, :name)';
    }

    if ($ok) {
        $sth = $pdo->prepare($sql);
        $result = $sth->execute($data);
        if (!$id) {
            $id = $pdo->lastInsertId();
        }
        $message = ['color' => 'green', 'message' => 'Bicycle saved.'];
    }
    else {
        $message = ['color' => 'red', 'message' => 'Cannot save bicycle, exceeds 50kg.'];
    }
}
$sth = $pdo->prepare('SELECT * FROM bicycles WHERE id = :id');
$sth->execute([':id' => $id]);
$bicycle = $sth->fetchAll();
$parts = [];
if (isset($bicycle[0])) {
    $parts = strlen($bicycle[0]['parts']) ? json_decode($bicycle[0]['parts'], true) : [];
}
?>
<html>
<head>
    <title>Add a bicycle</title>
</head>
<body>
<a href="./">Back</a>
<?php if ($message) { ?>
    <div style="background-color: <?php echo $message['color']; ?>"><?php echo $message['message']; ?></div>
<?php } ?>
<form id="bicycle-form" action="" method="post">
    <?php if (isset($bicycle[0]['id'])) { ?>
        <input type="hidden" name="id" value="<?php echo $bicycle[0]['id']; ?>">
    <?php } ?>
    <div>
        <label for="name">Name</label>
        <input name="name" id="name" value="<?php echo isset($bicycle[0]['name']) ? $bicycle[0]['name'] : ''; ?>">
    </div>
    <div>
        <label for="brand">Brand</label>
        <input name="brand" id="brand" value="<?php echo isset($bicycle[0]['brand']) ? $bicycle[0]['brand'] : ''; ?>">
    </div>
    <hr>
    <h2>Parts</h2>
    <button type="button" id="add-part">Add part</button>
    <table>
        <tbody id="parts-table">
        <tr>
            <th>Name</th>
            <th>Weight</th>
            <th>Price</th>
            <th>&nbsp;</th>
        </tr>
        <?php foreach ($parts as $id => $part) { ?>
            <tr>
                <td>
                    <input type="text" name="names[<?php echo $id; ?>]" value="<?php echo $part['name']; ?>">
                </td>
                <td>
                    <input type="text" class="weights" name="weights[<?php echo $id; ?>]"
                           value="<?php echo $part['weight']; ?>">
                </td>
                <td>
                    <input type="text" class="prices" name="prices[<?php echo $id; ?>]"
                           value="<?php echo $part['price']; ?>">
                </td>
                <td>
                    <button type="button" class="remove-part">Remove</button>
                </td>
            </tr>
        <?php } ?>
        <tr id="add-new" style="display: none;">
            <td>
                <input type="text" name="names[]">
            </td>
            <td>
                <input type="text" class="weights" name="weights[]">
            </td>
            <td>
                <input type="text" class="prices" name="prices[]">
            </td>
            <td>
                <button type="button" class="remove-part">Remove</button>
            </td>
        </tr>
        </tbody>
        <tfoot>
        <tr>
            <td><strong>Total:</strong></td>
            <td><span id="show-weights"><?php echo isset($bicycle[0]['weight']) ? $bicycle[0]['weight'] : 0; ?></span>kg</td>
            <td><span id="show-prices"><?php echo isset($bicycle[0]['price']) ? $bicycle[0]['price'] : 0; ?></span>€</td>
            <td></td>
        </tr>
        </tfoot>
    </table>
    <button type="submit">Save bicycle</button>
</form>
<script>
    document.getElementById('bicycle-form').onsubmit = function () {
        var calcedWeight = showMeasure('weights');
        var calcedPrice = showMeasure('prices');
        if (calcedWeight > 50) {
            alert('Weight exceeds 50kg, cannot save.');
            return false;
        }
        return true;
    };

    function showPrices() {
        showMeasure('prices');
    }

    function showWeights() {
        showMeasure('weights');
    }

    function showMeasure(what) {
        var measures = document.getElementsByClassName(what);
        var measure = 0;
        for (var i = 0; i < measures.length; i++) {
            measure += Number(measures[i].value);
        }
        document.getElementById('show-' + what).innerHTML = measure;
        return measure;
    }

    function remove() {
        this.parentNode.parentNode.remove();
    }

    function attachRemove() {
        var removes = document.getElementsByClassName('remove-part');
        for (var i = 0; i < removes.length; i++) {
            removes[i].onclick = remove;
        }
        var weights = document.getElementsByClassName('weights');
        for (i = 0; i < weights.length; i++) {
            weights[i].onchange = showWeights;
            weights[i].onkeydown = showWeights;
            weights[i].onkeyup = showWeights;
        }
        var prices = document.getElementsByClassName('prices');
        for (i = 0; i < prices.length; i++) {
            prices[i].onchange = showPrices;
            prices[i].onkeydown = showPrices;
            prices[i].onkeyup = showPrices;
        }
    }

    document.getElementById('add-part').onclick = function () {
        var newRow = document.getElementById('add-new').cloneNode(true);
        newRow.style.display = '';
        document.getElementById('parts-table').appendChild(newRow);
        attachRemove();
    };

    attachRemove();
</script>
</body>
</html>
