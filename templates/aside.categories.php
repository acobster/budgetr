<div id="categories">
    <h2>Categories</h2>
    <p><a href="./?view=categories">Edit Categories</a></p>
    <table>
        <tbody id="catList">
        
        <tr><th>Category</th><th>Total</th></tr>
    
        <?php foreach( $categories as $name => $cat ) : ?>
        <tr>
            <td>
                <?= $name ?>
            </td>
            <td class="amount" title="total for '<?= $name ?>'">
                    $<?= $this->formatAmt( $cat['subtotal'] ) ?>
            </td>
        </tr>
        <?php endforeach; ?>

        </tbody>
    </table>
</div>