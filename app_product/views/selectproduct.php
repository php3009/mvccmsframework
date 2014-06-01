<script type="text/javascript">
	function saveForm() {
		//alert('ok');
		document.adminForm.submit();
	}
</script>

<table border="0" width="100%">
	<tr>
		<td width="50%">
			<span class="application_title">Product Manager :</span><span class="application_job">[Select Product]</span>
		</td>
		<td align="right">
			<a href="index.php?app=app_product">
				<img src="<?php echo 'templates/' . 'default'; ?>/images/cancel.png" class="toolbarimage">
			</a>
		</td>
	</tr>
</table><br />

<form name="adminForm" id="adminForm" method="post">
	<table width="100%">
		<tr>
			<td align="right" class="greyBg" valign="top">
				Select Product 
			</td>
			<td>
				<input type="radio" name="producttype" id="simpleproduct" value="simpleproduct" checked='checked'/><label for="simpleproduct" class="adminTableText">Simple Product</label>&nbsp;&nbsp;
				<input type="radio" name="producttype" id="attributeproduct" value="attributeproduct"/><label for="attributeproduct" class="adminTableText">Product with attributes</label>&nbsp;&nbsp;<input type="button" value="Go" class="generate" onclick="saveForm();"><br /><br />
			</td>
		</tr>
	</table>
	<input type="hidden" name="job" value="getproductview">
	<input type="hidden" name="app" value="app_product">
</form>

