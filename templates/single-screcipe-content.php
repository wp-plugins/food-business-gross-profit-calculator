<?php
$screcipe_terms = get_the_terms( $post->ID,'screcipe_categories');
$sep='';
$output='';
if($screcipe_terms){
	foreach($screcipe_terms as $screcipe_term) {
		$output.=$sep.'<a href="'.get_term_link($screcipe_term).'">'.$screcipe_term->name.'</a>';
		if($sep==''){$sep=' / ';}
	}
	echo '<h3>Under Recipe Category: '.trim($output, $sep).'</h3>';
}

$post_modified = new DateTime($post->post_modified);
$curdate = new DateTime();

$last_modified = $curdate->diff($post_modified);
$pricewatch='';
//checks if more than 6months
if(($last_modified->y>0) || ($last_modified->m > 6)){
	$pricewatch='<span class="notes warning"><icon class="dashicons dashicons-clock"></icon>Page is updated more than 6 months ago and prices of the ingredients may not reflect the current prices in the market. Please update if needed. Thanks!';
}
?>
<!-- checks if prices updated more than 6months -->
<div id="pricewatch"><?php echo $pricewatch;?></div>
<?php echo $content; ?>
<?php
  $donateButton='<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top"><input name="cmd" type="hidden" value="_s-xclick" />
<input name="hosted_button_id" type="hidden" value="W7HKSYRWYFB3S" />
<input style="width:100px" alt="PayPal - The safer, easier way to pay online!" name="submit" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" type="image" />
<img src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" alt="" width="1" height="1" border="0" /></form>';

?>
<div id="tblcontainer" post_id="<?php echo $post->ID;?>">
	<h3>Ingredients in Details with Price Calculator below the table.</h3>

	<span class="labeltotal">Total Ingredients/Items Cost: <span class="currency">$</span> <span class="total_price">0</span></span><br/>
	<span class="labeltotal">Gross Profit Percentage: <span class="gpp">20</span> %</span><br/>
	<span class="labeltotal">Total Items/Servings Produced: <span class="total_serving">0</span></span><br/>
	<span class="labeltotal">Presumed Operating Expenses: <span class="currency">$</span> <span class="operating_expenses">0</span></span><br/>
	<span class="labeltotal">Price per Serving/Item (operating expenses EXcluded): <span class="currency">$</span> <span class="price_ex">0</span></span><br/>
	<span class="labeltotal">Price per Serving/Item (operating expenses INcluded): <span class="currency">$</span> <span class="price_in">0</span></span><br/><br/>
    <table id="tbl_screcipe" class="display" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>Ingredients/Items</th>

                <th>Measurements</th>

                <th>Prices</th>
            </tr>

        </thead>

        <tfoot>
            <tr>
                <th>Ingredients/Items</th>

                <th>Measurements</th>

                <th>Prices</th>
            </tr>
            <tr id="totalrow">
                <td colspan="2">Totals</td>

                <td id="total_price"><span class="currency">$</span> <span class="total_price"></span></td>

            </tr>
        </tfoot>

        <tbody>

        </tbody>
    </table>
</div>
<br/><br/><br/>
<div id="addform_screcipe">
	<span class="label">Ingredient's Name</span><br/>
	<input type="text" id="ingredient_item" class="width-100"/><br/><br/>
	<span class="label">Ingredient's Measurement</span><br/>
	<input type="text" id="ingredient_measurement" class="width-100"/><br/>
	<span class="notes warning"><icon class="dashicons dashicons-welcome-write-blog"></icon>Please be specific on the measurements. Specify if in pieces, grams and etc..</span><br/><br/>
	<span class="label">Ingredient's Price <span class="currency"></span></span>
	<input type="text" id="ingredient_price" class="width-100"/><br/>
	<span class="notes warning"><icon class="dashicons dashicons-welcome-write-blog"></icon>Please input only numbers as the item's price.</span>
	<br/><br/>
	<button class="add_ingredient submit"><icon class="dashicons-plus dashicons"></icon>Add Ingredient/Item</button>
	<br/><br/><br/>
	<h3>Pricing Calculator</h3><br/>
	<span class="labeltotal">Total Ingredients/Items Cost: <span class="currency">$</span> <span class="total_price">0</span></span><br/>
	<span class="labeltotal">Gross Profit Percentage: <span class="gpp">20</span> %</span><br/>
	<span class="labeltotal">Total Items/Servings Produced: <span class="total_serving">0</span></span><br/>
	<span class="labeltotal">Presumed Operating Expenses: <span class="currency">$</span> <span class="operating_expenses">0</span></span><br/>
	<span class="labeltotal">Price per Serving/Item (operating expenses EXcluded): <span class="currency">$</span> <span class="price_ex">0</span></span><br/>
	<span class="labeltotal">Price per Serving/Item (operating expenses INcluded): <span class="currency">$</span> <span class="price_in">0</span></span><br/><br/><br/>
	<div id="screcipesettings">
		<span class="label">Total Items/Servings Produced</span><br/>
		<input type="text" id="screcipe_tot_servings" class="width-100" value="0"/><br/><br/>
		<span class="label">Currency (<span class="currency">$</span>)</span><br/>
		<input type="text" id="screcipe_currency" class="width-100" value="$"/><br/><br/>
		<span class="label">Gross Profit Percentage (100%)</span><br/>
		<input type="text" id="screcipe_gpp" class="width-100" value="20"/><br/><br/>
		<span class="label">Presumed Operating Expenses in (<span class="currency">$</span>)</span><br/>
		<input type="text" id="screcipe_operating_expenses" class="width-100" value="0"/><br/><br/>
		<button class="submit update_settings"><icon class="dashicons-admin-generic dashicons"></icon>Calculate</button>

	</div>
</div>

<!--
created by:
  _________.__                  __      _________
 /   _____/|  |__   ___________|  | __ /   _____/_____   ____ _____ _______
 \_____  \ |  |  \_/ __ \_  __ \  |/ / \_____  \\____ \_/ __ \\__  \\_  __ \
 /        \|   Y  \  ___/|  | \/    <  /        \  |_> >  ___/ / __ \|  | \/
/_______  /|___|  /\___  >__|  |__|_ \/_______  /   __/ \___  >____  /__|
        \/      \/     \/           \/        \/|__|        \/     \/

http://www.sherkspear.com
-->