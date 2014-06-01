<script type="text/javascript">
	
	var chkFlag = 0;
	function checkUncheckAll() {
		var field = document.adminForm.list;
		if(chkFlag == 0) {
			for (i = 0; i < field.length; i++) {
				field[i].checked = true ;
			}
		}
		else {
			for (i = 0; i < field.length; i++) {
				field[i].checked = false ;
			}
		}
		if(chkFlag == 0) {
			chkFlag = 1;
		}
		else {
			chkFlag = 0;
		}
	}

	function edit() {
		var field = document.adminForm.list;
		var count = 0;
		for (i = 1; i < field.length; i++) {
			if(field[i].checked == true){
				count++;
			}
		}
		if(count == 1) {
			var id = 0;
			for (i = 1; i < field.length; i++) {
				if(field[i].checked == true){
					id = field[i].value;	
				} 
			}
			document.location = 'index.php?app=app_product&job=edit&id=' + id;
		}
		else {
			alert('Please select a single Product to edit');
		}
	}

	function deleteids() {
		var field = document.adminForm.list;
		var count = 0;
		for (i = 1; i < field.length; i++) {
			if(field[i].checked == true){
				count++;
			}
		}
		
		var deleteString = '';
		if(count > 0) {
			for (i = 1; i < field.length; i++) {
				if(field[i].checked == true){
					deleteString += field[i].value + '_';	
				}
			}
			document.location = 'index.php?app=app_product&job=deleteids&deleteString=' + deleteString;
		}
		else {
			alert('Please select Product to delete');
		}
	}

	function publishids() {
		var field = document.adminForm.list;
		var count = 0;
		for (i = 1; i < field.length; i++) {
			if(field[i].checked == true){
				count++;
			}
		}
		
		var publishString = '';
		if(count > 0) {
			for (i = 1; i < field.length; i++) {
				if(field[i].checked == true){
					publishString += field[i].value + '_';	
				}
			}
			document.location = 'index.php?app=app_product&job=publishids&publishString=' + publishString;
		}
		else {
			alert('Please select Product to publish');
		}
	}

	function unpublishids() {
		var field = document.adminForm.list;
		var count = 0;
		for (i = 1; i < field.length; i++) {
			if(field[i].checked == true){
				count++;
			}
		}
		
		var unpublishString = '';
		if(count > 0) {
			for (i = 1; i < field.length; i++) {
				if(field[i].checked == true){
					unpublishString += field[i].value + '_';	
				}
			}
			document.location = 'index.php?app=app_product&job=unpublishids&unpublishString=' + unpublishString;
		}
		else {
			alert('Please select Product to unpublish');
		}
	}

	function savesortorder() {
		document.adminForm.submit();
	}

</script><?php
	if(!empty($this->msg)) {
		?><div class="flagMsg"><div class="flagMsgText"><?php echo $this->msg; ?></div></div><?php
	}
?>
<table border="0" width="100%">
	<tr>
		<td width="50%">
			<div class="application_title">Product Manager</div>
		</td>
		<td align="right">
			<a href="index.php?app=app_home">
				<img src="<?php echo 'templates/' . 'default'; ?>/images/cancel.png" class="toolbarimage">
			</a>
			<a href="javascript:void(0)" onclick="publishids();">
				<img src="<?php echo 'templates/' . 'default'; ?>/images/publish.png" class="toolbarimage">
			</a>
			<a href="javascript:void(0)" onclick="unpublishids();">
				<img src="<?php echo 'templates/' . 'default'; ?>/images/unpublish.png" class="toolbarimage">
			</a>
			<a href="javascript:void(0)" onclick="deleteids();">
				<img src="<?php echo 'templates/' . 'default'; ?>/images/delete.png" class="toolbarimage">
			</a>
			<a href="javascript:void(0)" onclick="edit();">
				<img src="<?php echo 'templates/' . 'default'; ?>/images/edit.png" class="toolbarimage">
			</a>
			<a href="index.php?app=app_product&job=selectproduct">
				<img src="<?php echo 'templates/' . 'default'; ?>/images/add.png" class="toolbarimage">
			</a>
		</td>
	</tr>
</table>
<form name="adminForm" id="adminForm" method="post">
	<table width="100%" class="admintable" cellspacing="0">
		<th width="5%">
			<input type="checkbox" name="list" id="chkAll" onclick="checkUncheckAll();" value="0">
		</th>
		<th>
			Name
		</th>
		<th>
			Category
		</th>
		<th>
			Vendor
		</th>
		<th>
			Price
		</th>
		<th>
			Published
		</th>
		<th>
			Order&nbsp;&nbsp;<img src="<?php echo 'templates/' . 'default'; ?>/images/savesortorder.png" onclick="savesortorder();" style="cursor: pointer;">
		</th><?php
		$rowClassFlag = 0;
		while($row = $this->db->fetch($data)) {
			$editStr = 'edit';
			$editFlag = $row['variant'];
			if($editFlag == 'Y') {
				$editStr = 'editattributeproduct';
			}
			?><tr class="row<?php echo $rowClassFlag; ?>">
				<td align="center">
					<input type="checkbox" name="list" id="art_<?php
					echo $row['id'];
				?>" name="art_<?php
					echo $row['id'];
				?>" value="<?php
					echo $row['id'];
				?>"></td>
				<td align="center">
					<a href="index.php?app=app_product&job=<?php echo $editStr;?>&id=<?php echo $row['id'];?>"><?php
						echo $row['name'];
					?></a>
				</td>
				<td align="center"><?php
						$catIdsStr = '';
						$catIdsStr = $row['category_id'];
						if(!empty($catIdsStr)) {
							$qry2 = "SELECT id, name 
									FROM site_product_categories
									WHERE id IN ({$catIdsStr})";
							$data2 = $this->db->query($qry2);

							while($row2 = $this->db->fetch($data2)) { 
								?><a href="index.php?app=app_productcategory&job=edit&id=<?php echo $row2['id']; ?>"><?php
									echo $row2['name'];
								?></a><br /><?php	
							}
						}
				?></td>
				<td align="center"><?php
					if(!empty($row['vendor_id'])) {
						?><a href="index.php?app=app_vendor&job=edit&id=<?php echo $row['vendor_edit_id'];?>"><?php
							echo $row['vendor_id'];
						?></a><?php
					}
				?></td>
				<td align="center">
					<a href="index.php?app=app_product&job=<?php echo $editStr;?>&id=<?php echo $row['id'];?>"><?php
						echo $row['price'];
					?></a>
				</td>
				<td align="center"><?php
					if($row['published'] == 'Y') {
						?><a href='index.php?app=app_product&job=unpublish&id=<?php echo $row['id'];?>'><img src='<?php echo 'templates/' . 'default'; ?>/images/tick.png' border='0'></a><?php
					}
					else {
						?><a href='index.php?app=app_product&job=publish&id=<?php echo $row['id'];?>'><img src='<?php echo 'templates/' . 'default'; ?>/images/untick.png' border='0'></a><?php
					}
				?></td>
				<td align="center"><input name="orderid_<?php echo $row['id']; ?>" id="orderid_<?php echo $row['id']; ?>" type="text" class="adminTableInputTextSortorder" value="<?php
					echo $row['ordering'];
				?>"></td>
			</tr><?php
			if($rowClassFlag == 0) {
				$rowClassFlag =1;
			}
			else {
				$rowClassFlag = 0;
			}
		}
	?></table>
	<input type="hidden" name="job" value="savesortorder">
</form>

