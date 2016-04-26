<?php
require 'config.php';
if ($_POST) {

}
$sth = $pdo->prepare('SELECT * FROM bicycles WHERE id = :id');
$sth->execute([':id' => $_REQUEST['id']]);
$bicycle = $sth->fetchAll();
$parts = [];
if (isset($bicycle[0])) {
    $parts = strlen($bicycle[0]['parts']) ? json_decode($bicycle[0]['parts']) : [];
}
?>
<html>
<head>
    <title>Add a bicycle</title>
</head>
<body>
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
                    <input type="text" name="parts[<?php echo $id; ?>][name]" value="<?php echo $part['name']; ?>">
                </td>
                <td>
                    <input type="text" class="weights" name="parts[<?php echo $id; ?>][weight]"
                           value="<?php echo $part['weight']; ?>">
                </td>
                <td>
                    <input type="text" class="prices" name="parts[<?php echo $id; ?>][price]"
                           value="<?php echo $part['price']; ?>">
                </td>
                <td>
                    <button type="button" class="remove-part">Remove</button>
                </td>
            </tr>
        <?php } ?>
        <tr id="add-new" style="display: none;">
            <td>
                <input type="text" name="parts[][name]">
            </td>
            <td>
                <input type="text" class="weights" name="parts[][weight]">
            </td>
            <td>
                <input type="text" class="prices" name="parts[][price]">
            </td>
            <td>
                <button type="button" class="remove-part">Remove</button>
            </td>
        </tr>
        </tbody>
        <tfoot>
        <tr>
            <td><strong>Total:</strong></td>
            <td><span id="show-weights">0</span>kg</td>
            <td><span id="show-prices">0</span>€</td>
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
    };

    function showMeasure(what) {
        var measures = this.getElementsByClassName(what);
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
    }

    document.getElementById('add-part').onclick = function () {
        var newRow = document.getElementById('add-new').cloneNode(true);
        newRow.style.display = '';
        document.getElementById('parts-table').innerHTML += newRow.outerHTML;
        attachRemove();
    };

    attachRemove();
</script>
</body>
</html>
