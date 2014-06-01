<script type="text/javascript">
	var XMLHttpRequestObject = false;
	if (window.XMLHttpRequest) {
		XMLHttpRequestObject = new XMLHttpRequest();
	}
	else if (window.ActiveXObject) {
		XMLHttpRequestObject = new ActiveXObject("Microsoft.XMLHTTP");
	}
	
	function addnewbrand() {
		var newvendorbrand = document.getElementById('newvendorbrand').value;
		if(newvendorbrand == '') {
			document.getElementById('brandresponse').innerHTML = 'Enter the brand name';
		}
		else {
			if(XMLHttpRequestObject) {
				XMLHttpRequestObject.open("GET", "index.php?app=app_product&job=addnewbrand&newvendorbrand=" + newvendorbrand);
			}
			XMLHttpRequestObject.onreadystatechange = function() 
			{
				if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200) {
					if(XMLHttpRequestObject.responseText !== '') {
						responseArr = XMLHttpRequestObject.responseText.split('%%%%');	
						document.getElementById('newvendorbrand').value = '';
						document.getElementById('brandresponse').innerHTML = responseArr[0];
						if(responseArr[1] !== 'none') {
							document.getElementById('spanbrand').innerHTML = responseArr[1];
						}
					}
				}
			}
			XMLHttpRequestObject.send(null);
		}
	}

	var imgCnt = 1;
	
	function addImage() {
		imgCnt++;
		var imagespan = document.getElementById('imagespan');	
		
		var newspan = document.createElement('span');
		var idstr = 'file' + imgCnt;
		newspan.setAttribute('id', idstr);

		newspan.innerHTML = "<input type=\'radio\' value=\'"+imgCnt+"\' name=\'defaultimage\'>&nbsp;<input type=\'file\' name=\'file"+imgCnt+"\' id=\'file"+imgCnt+"\' class=\'adminTableInputText\'/>&nbsp;<a href=\'javascript:void(0);\' onclick=\'addImage();\' class=\'addnewimage\'>Add New</a><a href=\'javascript:void(0);\' onclick=\"removeImage(\'"+idstr+"\');\" class=\'addnewimage1\'>Remove</a><br/>";
		imagespan.appendChild(newspan);
	}

	function removeImage(obj) {
		var imagespan = document.getElementById('imagespan');	
		var oldspan = document.getElementById(obj);	 
		imagespan.removeChild(oldspan);
	}


	function add_related_products() {
		var productlistobj = document.getElementById('productlist');
		var related_productsobj = document.getElementById('related_products');
		
		var i;
		for(i=productlistobj.options.length-1;i>=0;i--) {
			if(productlistobj.options[i].selected) {
				var rpoption = document.createElement("option");
				rpoption.value= productlistobj.options[i].value; 
				rpoption.text = productlistobj.options[i].text;
				related_productsobj.options.add(rpoption);
				productlistobj.remove(i);
			}
		}
	}

	function remove_related_products() {
		var related_productsobj = document.getElementById('related_products');
		var productlistobj = document.getElementById('productlist');

		var i;
		for(i=related_productsobj.options.length-1;i>=0;i--) {
			if(related_productsobj.options[i].selected) {
				var proption = document.createElement("option");
				proption.value= related_productsobj.options[i].value; 
				proption.text = related_productsobj.options[i].text;
				productlistobj.options.add(proption);
				related_productsobj.remove(i);
			}
		}
	}

	function saveForm() {
		//alert('ok');
		var related_productsobj = document.getElementById('related_products');
		var i;

		for(i=related_productsobj.options.length-1;i>=0;i--) {
			related_productsobj.options[i].selected = true;
		}
		
		document.adminForm.submit();
	}
</script>
<script src="<?php echo 'templates/' . 'default'; ?>/js/SpryTabbedPanels.js" type="text/javascript"></script>
<script src="<?php echo 'templates/' . 'default'; ?>/js/calendar_eu.js" type="text/javascript"></script>
<link rel="stylesheet" href="<?php echo 'templates/' . 'default'; ?>/css/calendar.css" type="text/css" charset="utf-8" />
<table border="0" width="100%">
	<tr>
		<td width="50%">
			<span class="application_title">Product Manager :</span><span class="application_job">[Simple - Add]</span>
		</td>
		<td align="right">
			<a href="javascript:void(0)" onclick="saveForm();">
				<img src="<?php echo 'templates/' . 'default'; ?>/images/save.png" class="toolbarimage">
			</a>
			<a href="index.php?app=app_product">
				<img src="<?php echo 'templates/' . 'default'; ?>/images/cancel.png" class="toolbarimage">
			</a>
		</td>
	</tr>
</table><br />
<form name="adminForm" id="adminForm" method="post" enctype="multipart/form-data">
	<div id="TabbedPanels1" class="TabbedPanels">
		<ul class="TabbedPanelsTabGroup">
			<li class="TabbedPanelsTab" tabindex="0">Product Information</li>
			<li class="TabbedPanelsTab" tabindex="0">Product Status</li>
			<li class="TabbedPanelsTab" tabindex="0">Product Images</li>
			<li class="TabbedPanelsTab" tabindex="0">Related Products</li>
		</ul>
		<div class="TabbedPanelsContentGroup">
			<div class="TabbedPanelsContent"><br/>
				<table width="100%">
					<tr>
						<td align="right" class="greyBg">
							Name
						</td>
						<td>
							<input type="text" name="name" id="name" class="adminTableInputText"/>
						</td>
					</tr>
					<!-- <tr>
						<td align="right" class="greyBg">
							Vendor
						</td>
						<td>
							<select name="vname" class="adminTableSelect">
								<option value="0">-None-</option><?php
								if(count($vendorArr) > 0) {
									foreach($vendorArr AS $key => $value) {
										?><option value="<?php echo $key; ?>"><?php echo $value; ?></option><?php
									}
								}
							?></select>
						</td>
					</tr> -->
					<tr>
						<td align="right" class="greyBg" valign="top">
							Product Category
						</td>
						<td>
							<select multiple="multiple" size="10" name="parent[]" id="parent" class="adminTableSelect"><?php
							$parentCatIdsArr = array();
							$parentCatIdsArr = $this->getAllParentCatIds();	
							if(count($parentCatIdsArr) > 0) {
								foreach($parentCatIdsArr AS $key => $value) {
								?><option value="<?php echo $key; ?>"><?php echo $value; ?></option><?php
									$childCatIdsArr = array();
									$this->getAllChildCatIds($key);
									if(count($this->catIdsArr) > 0) {
										foreach($this->catIdsArr AS $key2 => $value2) {
											$spaceStr = $this->getSpace($key2);
											?><option value="<?php echo $key2; ?>"><?php echo $this->spaceStr . "|_>> " . $value2; ?></option><?php
											$this->spaceStr = '';
										}
									}
									$this->catIdsArr = array(); 
								}
							}
						?></select>
						</td>
					</tr>
					<tr>
						<td align="right" class="greyBg">
							Use Existing Brands
						</td>
						<td>
							<span id="spanbrand"><select name="vendorbrand" id="vendorbrand" class="adminTableSelect">
								<option value="0">-None-</option><?php
								foreach($vendorBrandsArr AS $key => $value) {
									?><option value="<?php echo $key; ?>"><?php echo $value; ?></option><?php
								}
							?></select></span><span style="color:#666666;font-weight:bold;">&nbsp;&nbsp;OR&nbsp;&nbsp;</span><span style="color: #666666;font-family: Arial,Helvetica,sans-serif;font-size: 11px;font-weight: bold;width: 180px;">New Brand&nbsp;</span><input type="text" name="newvendorbrand" id="newvendorbrand" class="adminTableInputText"/>&nbsp;<input type="button" value="Create" class="generate" onclick="addnewbrand();">&nbsp;<span id="brandresponse" style="color:#666666;font-family: Arial,Helvetica,sans-serif;font-size: 11px;font-weight: bold;"></span>
						</td>
					</tr>
					<tr>
						<td align="right" class="greyBg">
							Product Code
						</td>
						<td>
							<input type="text" name="code" id="code" class="adminTableInputText"/>
						</td>
					</tr>
					<tr>
						<td align="right" class="greyBg">
							SKU
						</td>
						<td>
							<input type="text" name="sku" id="sku" class="adminTableInputText"/>
						</td>
					</tr>
					<tr>
						<td align="right" class="greyBg">
							Price
						</td>
						<td>
							<input type="text" name="price" id="price" class="adminTableInputText"/>
						</td>
					</tr>
					<tr>
						<td align="right" class="greyBg">
							Discounted Price
						</td>
						<td>
							<input type="text" name="dprice" id="dprice" class="adminTableInputText"/>
						</td>
					</tr>
					<tr>
						<td align="right" valign="top" class="greyBg">
							Published
						</td>
						<td>
							<input type="radio" name="published" id="publishedN" value="N" checked='checked'/><label for="publishedN" class="adminTableText">No</label>&nbsp;&nbsp;
							<input type="radio" name="published" id="publishedY" value="Y"/><label for="publishedY" class="adminTableText">Yes</label><br /><br />
						</td>
					</tr>
					<tr>
						<td align="right" class="greyBg">
							SEF URL
						</td>
						<td>
							<input type="text" name="sefurl" id="sefurl" class="adminTableInputText"/>
						</td>
					</tr>
					<tr>
						<td align="right" class="greyBg" valign="top">
							Short Description
						</td>
						<td>
							<textarea name="short_desc" id="short_desc" class="adminTableTextArea" cols="50" rows="3"></textarea>
						</td>
					</tr>
					<tr>
						<td align="right" valign="top" class="greyBg">
							Description
						</td>
						<td><?php
							require_once(ADMINISTRATOR . DS . 'templates' . DS . 'default' . DS . 'ckeditor' . DS . 'ckeditor.php');
							$CKEditor = new CKEditor();
							$CKEditor->BasePath =  '/templates/' . 'default' . '/ckeditor/';
							$CKEditor->editor("description");	
							//$CKEditor->editor("textarea_id", "This is some sample text");
						?></td>
					</tr>
					<tr>
						<td colspan="2">
							<input type="hidden" name="job" id="job" value="save">
							<input type="hidden" name="app" id="app" value="app_product">
						</td>
					</tr>
				</table>
			</div>
			<div class="TabbedPanelsContent"><br/>
				<table width="100%">
					<!-- <tr>
						<td align="right" class="greyBg">&nbsp;</td>
						<td >
							<input type="checkbox" id="latest_sale_flag" name="latest_sale_flag" value="Y" />&nbsp;Latest Sales
							&nbsp;
							<input type="checkbox" id="fresh_arrivals_flag" name="fresh_arrivals_flag" value="Y" />&nbsp;Fresh Arrivals
							&nbsp;
							<input type="checkbox" id="featured_products_flag" name="featured_products_flag" value="Y" />&nbsp;Featured Products
							&nbsp;
							<input type="checkbox" id="best_sellers_flag" name="best_sellers_flag" value="Y" />&nbsp;Best Sellers
							&nbsp;
							<input type="checkbox" id="off_pricers_flag" name="off_pricers_flag" value="Y" />&nbsp;Off Pricers
							&nbsp;
							
							
						</td>
					</tr> -->
					<tr>
						<td align="right" class="greyBg">
							In Stock
						</td>
						<td>
							<input type="text" name="in_stock" id="in_stock" class="adminTableInputText"/>
						</td>
					</tr>
					<tr>
						<td align="right" class="greyBg">
							Minimum Purchase Quantity
						</td>
						<td>
							<input type="text" name="min_purchase_qty" id="min_purchase_qty" class="adminTableInputText"/>
						</td>
					</tr>
					<tr>
						<td align="right" class="greyBg">
							Maximum Purchase Quantity
						</td>
						<td>
							<input type="text" name="max_purchase_qty" id="max_purchase_qty" class="adminTableInputText"/>
						</td>
					</tr>
					<tr>
						<td align="right" class="greyBg">
							Available Date
						</td>
						<td>
							<input type="text" name="available_date" id="available_date" class="adminTableInputText"/>
							<script language="JavaScript">
								new tcal ({
									// form name
									'formname': 'adminForm',
									// input name
									'controlname': 'available_date'
								});
							</script>
						</td>
					</tr>
				</table>
			</div>
			<div class="TabbedPanelsContent"><br />
				<table>
					<tr>
						<td align="right" class="greyBg" valign="top">
							Default Image
						</td>
						<td>
							<span id="imagespan">
								<span>
									<input type="radio" value="1" name="defaultimage">
									<input type="file" name="file1" id="file1" class="adminTableInputText"/>&nbsp;<a href="javascript:void(0);" onclick="addImage();" class="addnewimage">Add New</a><br />
								</span>
							</span>
						</td>
					</tr>
				</table>
			</div>
			<div class="TabbedPanelsContent"><br />
				<table>
					<tr>
						<td class="greyBg2" align="center">Products<br><?php
							$productArr = array();
							$productArr = $this->getAllProducts($this->adminId);
							?><select multiple="multiple" size="10" id="productlist" class="adminTableSelect"><?php
								foreach($productArr AS $key => $value) {
									?><option value="<?php echo $key; ?>"><?php echo $value; ?></option><?php
								}
							?></select>
						</td>
						<td>
							<input type="button" value=">>" onclick="add_related_products();"><br />
							<input type="button" value="<<" onclick="remove_related_products();">
						</td>
						<td class="greyBg2" align="center">
							Related Products<br>
							<select multiple="multiple" size="10" id="related_products" name="related_products[]" class="adminTableSelect">
							</select>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</form>
<script type="text/javascript">
	<!--
	var TabbedPanels1 = new Spry.Widget.TabbedPanels("TabbedPanels1");
	//-->
</script>