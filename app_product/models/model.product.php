<?php

// no direct access
defined('EXEC') or die('Restricted access');

require_once(APPLICATIONS . DS . 'app_productcategory' . DS . 'product_configuration.php');

class product {
	
	var $db;
	var $config;
	var $adminId;
	var $product_config;
	var $catIdsArr = array();
	var $spaceStr;
	var $product_image_cnt;
	var $flagMsg;
	var $msg;


	function product() {

		//Database Object
		$this->db = new dbconn();
		
		/*Configuration object*/
		$this->config = new config();

		/*Admin Id*/
		$this->adminId = $_SESSION['adminid'];
		
		$this->product_config = new product_config();

		$this->spaceStr = '';

		$this->flagMsg = '';

		if(isset($_REQUEST['flagMsg']) && !empty($_REQUEST['flagMsg'])) {
			$this->flagMsg = htmlentities($_REQUEST['flagMsg'], ENT_QUOTES);
		}

		$this->msg = '';
		switch($this->flagMsg) {
			case 'saved':
				$this->msg = 'Product Saved';	
				break;
			case 'deleted':
				$this->msg = 'Product Deleted Successfully';
				break;
			case 'published':
				$this->msg = 'Product Published Successfully';
				break;
			case 'unpublished':
				$this->msg = 'Product Unpublished Successfully';
				break;
			case 'sortordersaved':
				$this->msg = 'Sort Order Saved Successfully';
				break;
			default:
				$this->msg = '';
				break;
		}
	}

	//Switch job
	function performjob($job) {
		
		switch ($job) {
			case 'selectproduct':
				$this->selectproduct();
				break;
			case 'getproductview':
				$this->getproductview();
				break;
			case 'add':
				$this->add();
				break;
			case 'save':
				/*echo "<pre>";
				print_r($_REQUEST);
				echo "</pre>";
				echo "<pre>";
				print_r($_FILES); 
				echo "</pre>";//exit;*/
				$this->save();
				header('Location: index.php?app=app_product&flagMsg=saved');
				break;
			case 'saveattributeproduct':
				$this->saveattributeproduct();
				header('Location: index.php?app=app_product&flagMsg=saved');	
				break;
			case 'edit':
				$this->edit();
				break;
			case 'editattributeproduct':
				$this->editattributeproduct();
				break;
			case 'editsave':
				$this->editsave();
				header('Location: index.php?app=app_product&flagMsg=saved');
				break;
			case 'editsaveattributeproduct':
				$this->editsaveattributeproduct();
				header('Location: index.php?app=app_product&flagMsg=saved');
				break;
			case 'deleteids':
				$this->deleteids();
				header('Location: index.php?app=app_product&flagMsg=deleted');
				break;
			case 'deleteimage':
				$this->deleteimage();
				break;
			case 'getAttributes':
				$this->getAttributes();
				break;
			case 'publish':
				$this->publish();
				header('Location: index.php?app=app_product&flagMsg=published');
				break;
			case 'unpublish':
				$this->unpublish();
				header('Location: index.php?app=app_product&flagMsg=unpublished');
				break;
			case 'publishids':
				$this->publishids();
				header('Location: index.php?app=app_product&flagMsg=published');
				break;
			case 'unpublishids':
				$this->unpublishids();
				header('Location: index.php?app=app_product&flagMsg=unpublished');
				break;
			case 'savesortorder':
				$this->savesortorder();
				header('Location: index.php?app=app_product&flagMsg=sortordersaved');
				break;
			case 'addnewbrand':
				$this->addnewbrand();	
				break;
			default:
				$this->showAllProducts();
				break;
		}

	}

	//Show product selection screen
	function selectproduct() {
		include (APPLICATIONS . DS . 'app_product' . DS . 'views' . DS . 'selectproduct.php');
	}

	//Get Product View
	function getproductview() {
		$producttype = '';
		if(isset($_REQUEST['producttype']) && !empty($_REQUEST['producttype'])) {
			$producttype = $_REQUEST['producttype'];
		}
		$vendorArr = array();
		$vendorArr = $this->getAllVendors();
		$vendorBrandsArr = array();
		$vendorBrandsArr = $this->getVendorBrands();
		if($producttype == 'simpleproduct') {
			include (APPLICATIONS . DS . 'app_product' . DS . 'views' . DS . 'add.php');
		}
		else {
			require (APPLICATIONS . DS . 'app_product' . DS . 'views' . DS . 'addattributes.php');
		}
	}
	
	//Get All Vendor Brands
	function getVendorBrands() {
		$vendorBrandsArr = array();
		/*$qry = "SELECT * FROM site_vendor_brands
				WHERE vid = '{$this->vid}'";*/
		$qry = "SELECT * FROM site_vendor_brands";
		$data = $this->db->query($qry);
		while($row = $this->db->fetch($data)) {
			$vendorBrandsArr[$row['id']] = $row['name'];
		}
		return $vendorBrandsArr;
	}

	function addnewbrand() {
		ob_clean();
		$newvendorbrand = htmlentities($_REQUEST['newvendorbrand'], ENT_QUOTES);
		$qry = "SELECT COUNT(id) c FROM site_vendor_brands
				WHERE name LIKE '{$newvendorbrand}'";
		$data = $this->db->query($qry);
		$insertFlag = 0;
		while($row = $this->db->fetch($data)) {
			if($row['c'] > 0) {
				$insertFlag = 1;
			}
		}
		$responseText = '';
		if($insertFlag == 1) {
			$responseText .= "Brand Already Exists" . "%%%%" . "none";
		}
		else {
			$maxOrderingQry = "SELECT max(ordering) m FROM site_vendor_brands";
			$data = $this->db->query($maxOrderingQry);
			$maxOrdering = 0;
			while($result = $this->db->fetch($data)) {
				$maxOrdering = $result['m'];
			}
			$maxOrdering = $maxOrdering+1;
			$newvendorbrand = htmlentities($_REQUEST['newvendorbrand'], ENT_QUOTES);
			$qry = "INSERT INTO site_vendor_brands
					(associated_vids, name, ordering)
					VALUES ('{$this->adminId}', '{$newvendorbrand}', '{$maxOrdering}')";
			$this->db->query($qry);
			
			$nbid = 0;
			$nbid = $this->db->get_insert_id();
			
			$responseText .= "Brand Added Successfully" . "%%%%";
			
			$vendorBrandsArr = array();
			$qry = "SELECT * FROM site_vendor_brands";
			$data = $this->db->query($qry);
			while($row = $this->db->fetch($data)) {
				$vendorBrandsArr[$row['id']] = $row['name'];
			}
			$responseText .= "<select name='vendorbrand' id='vendorbrand' class='adminTableSelect'>";
			$responseText .= "<option value='0'>-None-</option>";
			foreach($vendorBrandsArr AS $key => $value) {
				$selectedText = '';
				if($key == $nbid) {
					$selectedText = "selected='selected'";
				}
				$responseText .= "<option value='" . $key . "'" . $selectedText . ">" . $value . "</option>";
			}
			$responseText .= "</select>";
		}
		echo $responseText;
		exit;
	}

	//Get All Products
	function showAllProducts() {
		$qry = "SELECT p.id id,
				p.vendor_id vid,
				CONCAT_WS(' ', v.title, v.f_name, v.l_name) vendor_id,
				v.id vendor_edit_id,
				p.category_id category_id,
				p.variant variant,
				p.sku sku,
				p.price price,
				p.name name,
				p.published published,
				p.ordering ordering
				FROM site_products p 
				LEFT JOIN site_vendors v
				ON p.vendor_id = v.user_id";
		$data = $this->db->query($qry);
		include (APPLICATIONS . DS . 'app_product' . DS . 'views' . DS . 'default.php');
	}
	
	//Show Add Form
	function add() {
		include (APPLICATIONS . DS . 'app_product' . DS . 'views' . DS . 'add.php');
	}

	//Show Edit Form
	function edit() {
		$qry = "SELECT * FROM site_products
				WHERE id = '{$_REQUEST['id']}'";
		$data = $this->db->query($qry);
		
		while($result = $this->db->fetch($data)) {
			$id = $result['id'];
			$vendor_id = $result['vendor_id'];
			$category_id = $result['category_id'];
			$brandid = $result['brandid'];
			$code = $result['code'];
			$sku = $result['sku'];
			$short_desc = $result['short_desc'];
			$long_desc = $result['long_desc'];
			$image = $result['image'];
			$price = $result['price'];
			$dprice = $result['dprice'];
			$in_stock = $result['in_stock'];
			$available_date = $result['available_date'];
			$name = $result['name'];
			$min_purchase_qty = $result['min_purchase_qty'];
			$max_purchase_qty = $result['max_purchase_qty'];
			$related_products = $result['related_products'];
			$published = $result['published'];
			
			$latest_sale_flag = $result['latest_sale_flag'];
			$fresh_arrivals_flag = $result['fresh_arrivals_flag'];
			$featured_products_flag = $result['featured_products_flag'];
			$best_sellers_flag = $result['best_sellers_flag'];
			$off_pricers_flag = $result['off_pricers_flag'];
			
		}

		$qry = "SELECT sefurl FROM site_manager
				WHERE realurl = 'index.php?app=app_shop&pid=" . $_REQUEST['id'] . "'
				AND application='app_shop'";
		
		$data = $this->db->query($qry);
		
		$sefurl = '';
		while($result = $this->db->fetch($data)) {
			$sefurl = $result['sefurl']; 
		}
		
		$vendorArr = array();
		$vendorArr = $this->getAllVendors();
		
		$vendorBrandsArr = array();
		$vendorBrandsArr = $this->getVendorBrands();

		include (APPLICATIONS . DS . 'app_product' . DS . 'views' . DS . 'edit.php');
	}

	function editattributeproduct() {
		$qry = "SELECT * FROM site_products
				WHERE id = '{$_REQUEST['id']}'";
		$data = $this->db->query($qry);

		while($result = $this->db->fetch($data)) {
			$id = $result['id'];
			$vendor_id = $result['vendor_id'];
			$category_id = $result['category_id'];
			$brandid = $result['brandid'];

			$short_desc = $result['short_desc'];
			$long_desc = $result['long_desc'];
			$image = $result['image'];
			$code = $result['code'];
			$price = $result['price'];
			$dprice = $result['dprice'];

			$available_date = $result['available_date'];
			$name = $result['name'];

			$min_purchase_qty = $result['min_purchase_qty'];
			$max_purchase_qty = $result['max_purchase_qty'];
			$related_products = $result['related_products'];
			$published = $result['published'];
			
			$latest_sale_flag = $result['latest_sale_flag'];
			$fresh_arrivals_flag = $result['fresh_arrivals_flag'];
			$featured_products_flag = $result['featured_products_flag'];
			$best_sellers_flag = $result['best_sellers_flag'];
			$off_pricers_flag = $result['off_pricers_flag'];
			
		}

		$qry = "SELECT sefurl FROM site_manager
				WHERE realurl = 'index.php?app=app_shop&pid=" . $_REQUEST['id'] . "'
				AND application='app_shop'";
		
		$data = $this->db->query($qry);
		
		$sefurl = '';
		while($result = $this->db->fetch($data)) {
			$sefurl = $result['sefurl']; 
		}
		
		$qry = "SELECT asid, avids, aprice, psku 
				FROM site_productvariants
				WHERE pid = '{$_REQUEST['id']}' 
				ORDER BY id";
		
		$data = $this->db->query($qry);
		
		$variantSetArr = array();
		$asid = 0;
		while($result = $this->db->fetch($data)) {
			$asid = $result['asid'];
			$variantSetArr[] = $result['asid'] . '||' . $result['avids'] . '||' . $result['aprice'] . '||' . $result['psku'];
		}

		$vendorArr = array();
		$vendorArr = $this->getAllVendors();

		$vendorBrandsArr = array();
		$vendorBrandsArr = $this->getVendorBrands();
		
		$attributeListEditStr = $this->getAttributeListEdit($vendor_id);
		$attributeListEditArr = array();
		$attributeListEditArr = explode("||", $attributeListEditStr);
		include (APPLICATIONS . DS . 'app_product' . DS . 'views' . DS . 'editattributes.php');
	}

	//Get All Categories  
	function getAllParentCatIds() {
		$qry = "SELECT * FROM site_product_categories
					WHERE parent_id='0'";
		
		$data = $this->db->query($qry);

		$parentCatIdsArr = array();

		while($result = $this->db->fetch($data)) {
			$parentCatIdsArr[$result['id']] = $result['name']; 
		}

		return $parentCatIdsArr;
	}
	
	//Get All Child Categories Recursively 
	function getAllChildCatIds($cid) {
		
		$qry = "SELECT * FROM site_product_categories
					WHERE parent_id='{$cid}'";
		
		$data = $this->db->query($qry);

		if($this->db->rows($data) > 0) {
				
			$parentCatIdsArr = array();	
			
			while($result = $this->db->fetch($data)) {
				$this->catIdsArr[$result['id']] = $result['name']; 

				$qry2 = "SELECT * FROM site_product_categories
					WHERE parent_id='{$result['id']}'";

								
				$data2 = $this->db->query($qry2);
				
				if($this->db->rows($data2) > 0) {
					while($result2 = $this->db->fetch($data2)) {
						$this->catIdsArr[$result2['id']] = $result2['name']; 
						$this->getAllChildCatIds($result2['id']);
					}
				}
			}
		}
	}
	
	//Get Space for child Categories
	function getSpace($cid) {
		$qry = "SELECT * FROM site_product_categories
					WHERE id='{$cid}'";
		
		$data = $this->db->query($qry);

		if($this->db->rows($data) > 0) {
			while($result = $this->db->fetch($data)) {
				$this->spaceStr .= "&nbsp;&nbsp;&nbsp;";
				
				$qry2 = "SELECT * FROM site_product_categories
					WHERE id='{$result['parent_id']}'";
								
				$data2 = $this->db->query($qry2);
				
				if($this->db->rows($data2) > 0) {
					while($result2 = $this->db->fetch($data2)) {
						if($result2['parent_id'] !== 0) {
							$this->spaceStr .= "&nbsp;&nbsp;";
							$this->getSpace($result2['id']);
						}
					}
				}
			}
		}
	}

	//Save to table
	function save() {
		
		$name = htmlentities($_REQUEST['name'], ENT_QUOTES);
		$vname = $this->adminId;
		$parentTempArr = array();
		$parentStr = '';
		if(isset($_REQUEST['parent'])) {
			foreach($_REQUEST['parent'] AS $key => $value) {
				$parentTempArr[] = htmlentities($value, ENT_QUOTES);
			}
			$parentStr = implode(',', $parentTempArr);
		}
		$code = htmlentities($_REQUEST['code'], ENT_QUOTES);
		$sku = htmlentities($_REQUEST['sku'], ENT_QUOTES);
		$price = htmlentities($_REQUEST['price'], ENT_QUOTES);
		$dprice = htmlentities($_REQUEST['dprice'], ENT_QUOTES);
		$published = $_REQUEST['published'];
		$short_desc = htmlentities($_REQUEST['short_desc'], ENT_QUOTES);
		$description = $_REQUEST['description'];
		$in_stock = htmlentities($_REQUEST['in_stock'], ENT_QUOTES);
		$min_purchase_qty = htmlentities($_REQUEST['min_purchase_qty'], ENT_QUOTES);
		$max_purchase_qty = htmlentities($_REQUEST['max_purchase_qty'], ENT_QUOTES);
		$available_dateTemp = htmlentities($_REQUEST['available_date'], ENT_QUOTES); 
		$available_dateTemp = explode("-", $available_dateTemp);
		
		/*Brand Id calculation*/
		$vendorbrandId = 0;
		$vendorbrand = htmlentities($_REQUEST['vendorbrand'], ENT_QUOTES);
		if($vendorbrand !== 0) {
			$vendorbrandId = $vendorbrand;
		}
		$qry = "SELECT associated_vids FROM site_vendor_brands
				WHERE id = '{$vendorbrand}'";
		$data = $this->db->query($qry);
		$associatedvidsArr = array();
		while($row = $this->db->fetch($data)) {
			$tempArr = array();
			$tempArr = explode(",", $row['associated_vids']);
			foreach($tempArr AS $value) {
				$associatedvidsArr[] = $value; 
			}
		}
		if(!in_array($this->adminId, $associatedvidsArr)) {
			$associatedvidsArr[] = $this->adminId;
			$associatedvidsStr = '';
			$associatedvidsStr = implode(",", $associatedvidsArr);
			$qry = "UPDATE site_vendor_brands
					SET associated_vids = '{$associatedvidsStr}'
					WHERE id = '{$vendorbrand}'";
			$this->db->query($qry);	
		}
		
		/* [show selected flag on front home page as slider][vimal_chauhan] [13-Apr-2011][start] */
		$latest_sale_flag = 'N';
		$fresh_arrivals_flag = 'N';
		$featured_products_flag = 'N';
		$best_sellers_flag = 'N';
		$off_pricers_flag = 'N';
		
		if(isset($_REQUEST['latest_sale_flag'])) {
			$latest_sale_flag = $_REQUEST['latest_sale_flag'];
		}
		if(isset($_REQUEST['fresh_arrivals_flag'])) {
			$fresh_arrivals_flag = $_REQUEST['fresh_arrivals_flag'];
		}
		if(isset($_REQUEST['featured_products_flag'])) {
			$featured_products_flag = $_REQUEST['featured_products_flag'];
		}
		if(isset($_REQUEST['best_sellers_flag'])) {
			$best_sellers_flag = $_REQUEST['best_sellers_flag'];
		}
		if(isset($_REQUEST['off_pricers_flag'])) {
			$off_pricers_flag = $_REQUEST['off_pricers_flag'];
		}
		
		/* [show selected flag on front home page as slider][vimal_chauhan] [13-Apr-2011][end] */
		$available_date = '';
		if(isset($available_dateTemp[2]) && isset($available_dateTemp[1]) && isset($available_dateTemp[0])) {
			$available_dateTemp = $available_dateTemp[2] . "-" . $available_dateTemp[1] . "-" . $available_dateTemp[0];
		}
		if($available_dateTemp == "--") {
			$available_dateTemp = '';
		}
		$available_date = $available_dateTemp;
		
		$related_products = '';
		$related_productsStr = '';
		if(isset($_REQUEST['related_products'])) {
			$related_productsStr = implode(',', $_REQUEST['related_products']);
			$related_products = $related_productsStr;
		}
		
		$maxOrderingQry = "SELECT max(ordering) m FROM site_products";
		$data = $this->db->query($maxOrderingQry);
		$maxOrdering = 0;
		while($result = $this->db->fetch($data)) {
			$maxOrdering = $result['m'];
		}
		$maxOrdering = $maxOrdering+1;
		/* [insert selected flag][vimal_chauhan] [13-Apr-2011][start] */
		$qry = "INSERT INTO site_products
				(vendor_id, category_id, brandid, variant, code, sku, short_desc, long_desc, price, dprice, in_stock, available_date, name, min_purchase_qty, max_purchase_qty, related_products, published, latest_sale_flag, fresh_arrivals_flag, featured_products_flag, best_sellers_flag, off_pricers_flag, ordering)
				VALUES ('{$vname}', '{$parentStr}', '{$vendorbrandId}', 'N', '{$code}', '{$sku}', '{$short_desc}', '{$description}', '{$price}', '{$dprice}', '{$in_stock}', '{$available_date}', '{$name}', '{$min_purchase_qty}', '{$max_purchase_qty}', '{$related_products}', '{$published}', '{$latest_sale_flag}', '{$fresh_arrivals_flag}', '{$featured_products_flag}', '{$best_sellers_flag}', '{$off_pricers_flag}', '{$maxOrdering}')"; 

		/* [insert selected flag][vimal_chauhan] [13-Apr-2011][start] */
		$this->db->query($qry);
		$pid = $this->db->get_insert_id(); 
		
		$sefurl = htmlentities($_REQUEST['sefurl'], ENT_QUOTES);

		$qry = "INSERT INTO site_manager
				(sefurl, realurl, application, published)
				VALUES ('$sefurl', 'index.php?app=app_shop&pid=$pid', 'app_shop', 'Y')";
		$this->db->query($qry); 

		/*Images*/
		$imageArr = array();
		$imageStr = '';
		$image = '';
		$imagePath = ROOT . DS . 'applications' . DS . 'app_shop' . DS . 'images' . DS . 'products' . DS . 'large'; 
		$mediumPath = ROOT . DS . 'applications' . DS . 'app_shop' . DS . 'images' . DS . 'products' . DS . 'medium';
		$smallPath = ROOT . DS . 'applications' . DS . 'app_shop' . DS . 'images' . DS . 'products' . DS . 'small';
		$thumbPath = ROOT . DS . 'applications' . DS . 'app_shop' . DS . 'images' . DS . 'products' . DS . 'thumbnail';

		$time = md5(microtime().rand(0,999999));
			
		if(count($_FILES) > 0) {
			$flag = 0;
			foreach($_FILES AS $key => $value) {
				if($_FILES[$key]["error"] == 0) { 
					if ((($_FILES[$key]["type"] == "image/gif") || ($_FILES[$key]["type"] == "image/jpeg") || ($_FILES[$key]["type"] == "image/pjpeg"))) {
						/*Default Values*/
						$largeImageWidth = 800;
						$largeImageHeight = 600;
						$mediumImageWidth = 400;
						$mediumImageHeight = 300;
						$smallImageWidth = 100;
						$smallImageHeight = 100;
						$thumbImageWidth = 50;
						$thumbimageHeight = 50;

						$size = getimagesize($_FILES[$key]["tmp_name"]);	

						$ratiowidth = round($size[1] / $size[0], 2);
						$ratioheight = round($size[0] / $size[1], 2);
						
						/*Large Image Width Height*/
						switch($this->product_config->product_large_image_flag) {
							case 'width':
								if(!empty($this->product_config->product_large_image_width)) {
									$largeImageWidth = $this->product_config->product_large_image_width;
									$largeImageHeight = round($largeImageWidth * $ratiowidth); 
								}
								break;
							case 'height':
								if(!empty($this->product_config->product_large_image_height)) {
									$largeImageHeight = $this->product_config->product_large_image_height; 
									$largeImageWidth = round($largeImageHeight * $ratioheight);
								}
								break;
						}

						/*Medium Image Width Height*/
						switch($this->product_config->product_medium_image_flag) {
							case 'width':
								if(!empty($this->product_config->product_medium_image_width)) {
									$mediumImageWidth = $this->product_config->product_medium_image_width;
									$mediumImageHeight = round($mediumImageWidth * $ratiowidth);
								}
								break;
							case 'height':
								if(!empty($this->product_config->product_medium_image_height)) {
									$mediumImageHeight = $this->product_config->product_medium_image_height;
									$mediumImageWidth = round($mediumImageHeight * $ratioheight);
								}
								break;
						}

						/*Small Image Width Height*/
						switch($this->product_config->product_small_image_flag) {
							case 'width':
								if(!empty($this->product_config->product_small_image_width)) {
									$smallImageWidth = $this->product_config->product_small_image_width;
									$smallImageHeight = round($smallImageWidth * $ratiowidth);
								}	
								break;
							case 'height':
								if(!empty($this->product_config->product_small_image_height)) {
									$smallImageHeight = $this->product_config->product_small_image_height;
									$smallImageWidth = round($smallImageHeight * $ratioheight);
								}
								break;
						}

						/*Thumbnail Image Width Height*/
						switch($this->product_config->product_thumb_image_flag) {
							case 'width':
								if(!empty($this->product_config->product_thumb_image_width)) {
									$thumbImageWidth = $this->product_config->product_thumb_image_width;
									$thumbimageHeight = round($thumbImageWidth * $ratiowidth);
								}
								break;
							case 'height':
								if(!empty($this->product_config->product_thumb_image_height)) {
									$thumbimageHeight = $this->product_config->product_thumb_image_height;
									$thumbImageWidth = round($thumbimageHeight * $ratioheight);
								}	
								break;
						}

						$imagename = $time . $_FILES[$key]["name"];
						$image = $_FILES[$key]["tmp_name"];
						$this->createImage($imagename, $image, $imagePath, "large-", $largeImageWidth, $largeImageHeight, 100);
						$this->createImage($imagename, $image, $mediumPath, "medium-", $mediumImageWidth, $mediumImageHeight, 100);
						$this->createImage($imagename, $image, $smallPath, "small-", $smallImageWidth, $smallImageHeight, 100);
						$this->createImage($imagename, $image, $thumbPath, "thumb-", $thumbImageWidth, $thumbimageHeight, 100);
						
						$tempImgCnt = str_replace('file', '', $key);
						$imageArr[$tempImgCnt] = $imagename;
						if(isset($_REQUEST['defaultimage'])) {
							if($_REQUEST['defaultimage'] == $tempImgCnt) {
								$flag = $tempImgCnt;
							}
						}
					}
				}
			}
			/*if($flag !== 0) {
				$flag = $flag - 1;
			}*/
			
			$imageArrDefaultFirstArr = array();
			foreach($imageArr AS $key => $value) {
				if($key == $flag) {
					$imageArrDefaultFirstArr[] = $value;
				}
			}
			
			unset($imageArr[$flag]);
			foreach($imageArr AS $key => $value) {
				$imageArrDefaultFirstArr[] = $value;
			}
			$imageStr = implode('|', $imageArrDefaultFirstArr);
		}

		if(!empty($imageStr)) {
			$qry = "UPDATE site_products
					SET image='{$imageStr}'
					WHERE id='{$pid}'";
			$this->db->query($qry);
		}
	}

	function saveattributeproduct() {
		$name = htmlentities($_REQUEST['name'], ENT_QUOTES);
		$vname = $this->adminId;
		$parentTempArr = array();
		$parentStr = '';
		if(isset($_REQUEST['parent'])) {
			foreach($_REQUEST['parent'] AS $key => $value) {
				$parentTempArr[] = htmlentities($value, ENT_QUOTES);
			}
			$parentStr = implode(',', $parentTempArr);
		}
		$code = htmlentities($_REQUEST['code'], ENT_QUOTES);
		$price = htmlentities($_REQUEST['price'], ENT_QUOTES);
		$published = $_REQUEST['published'];
		$short_desc = htmlentities($_REQUEST['short_desc'], ENT_QUOTES);
		$description = $_REQUEST['description'];
		/*$min_purchase_qty = htmlentities($_REQUEST['min_purchase_qty'], ENT_QUOTES);
		$max_purchase_qty = htmlentities($_REQUEST['max_purchase_qty'], ENT_QUOTES);*/
		$available_dateTemp = htmlentities($_REQUEST['available_date'], ENT_QUOTES); 
		$available_dateTemp = explode("-", $available_dateTemp);
		
		/*Brand Id calculation*/
		$vendorbrandId = 0;
		$vendorbrand = htmlentities($_REQUEST['vendorbrand'], ENT_QUOTES);
		if($vendorbrand !== 0) {
			$vendorbrandId = $vendorbrand;
		}
		$qry = "SELECT associated_vids FROM site_vendor_brands
				WHERE id = '{$vendorbrand}'";
		$data = $this->db->query($qry);
		$associatedvidsArr = array();
		while($row = $this->db->fetch($data)) {
			$tempArr = array();
			$tempArr = explode(",", $row['associated_vids']);
			foreach($tempArr AS $value) {
				$associatedvidsArr[] = $value; 
			}
		}
		if(!in_array($this->adminId, $associatedvidsArr)) {
			$associatedvidsArr[] = $this->adminId;
			$associatedvidsStr = '';
			$associatedvidsStr = implode(",", $associatedvidsArr);
			$qry = "UPDATE site_vendor_brands
					SET associated_vids = '{$associatedvidsStr}'
					WHERE id = '{$vendorbrand}'";
			$this->db->query($qry);	
		}

		/* [show selected flag on front home page as slider][vimal_chauhan] [13-Apr-2011][start] */
		$latest_sale_flag = 'N';
		$fresh_arrivals_flag = 'N';
		$featured_products_flag = 'N';
		$best_sellers_flag = 'N';
		$off_pricers_flag = 'N';
		
		if(isset($_REQUEST['latest_sale_flag'])) {
			$latest_sale_flag = $_REQUEST['latest_sale_flag'];
		}
		if(isset($_REQUEST['fresh_arrivals_flag'])) {
			$fresh_arrivals_flag = $_REQUEST['fresh_arrivals_flag'];
		}
		if(isset($_REQUEST['featured_products_flag'])) {
			$featured_products_flag = $_REQUEST['featured_products_flag'];
		}
		if(isset($_REQUEST['best_sellers_flag'])) {
			$best_sellers_flag = $_REQUEST['best_sellers_flag'];
		}
		if(isset($_REQUEST['off_pricers_flag'])) {
			$off_pricers_flag = $_REQUEST['off_pricers_flag'];
		}
		/* [show selected flag on front home page as slider][vimal_chauhan] [13-Apr-2011][end] */
		
		
		
		$available_date = '';
		if(isset($available_dateTemp[2]) && isset($available_dateTemp[1]) && isset($available_dateTemp[0])) {
			$available_dateTemp = $available_dateTemp[2] . "-" . $available_dateTemp[1] . "-" . $available_dateTemp[0];
		}
		if($available_dateTemp == "--") {
			$available_dateTemp = '';
		}
		$available_date = $available_dateTemp;

		$related_products = '';
		$related_productsStr = '';
		if(isset($_REQUEST['related_products'])) {
			$related_productsStr = implode(',', $_REQUEST['related_products']);
			$related_products = $related_productsStr;
		}

		$maxOrderingQry = "SELECT max(ordering) m FROM site_products";
		$data = $this->db->query($maxOrderingQry);
		$maxOrdering = 0;
		while($result = $this->db->fetch($data)) {
			$maxOrdering = $result['m'];
		}
		$maxOrdering = $maxOrdering+1;
	
		$qry = "INSERT INTO site_products
				(vendor_id, category_id, brandid, variant, code, short_desc, long_desc, price, available_date, name, related_products, published, latest_sale_flag, fresh_arrivals_flag, featured_products_flag, best_sellers_flag, off_pricers_flag, ordering)
				VALUES ('{$vname}', '{$parentStr}', '{$vendorbrandId}', 'Y', '{$code}', '{$short_desc}', '{$description}', '{$price}', '{$available_date}', '{$name}', '{$related_products}', '{$published}', '{$latest_sale_flag}', '{$fresh_arrivals_flag}', '{$featured_products_flag}', '{$best_sellers_flag}', '{$off_pricers_flag}', '{$maxOrdering}')";
		
		$this->db->query($qry);
		$pid = $this->db->get_insert_id(); 
		
		/*Variant products*/
		$prVariantKeyArr = array();
		foreach($_REQUEST AS $key => $value) {
			if(substr($key, 0, 5) == 'vmin_') {
				$tempKey = str_replace('vmin_', '', $key);
				$prVariantKeyArr[$tempKey] = array();
				$prVariantKeyArr[$tempKey][] = $value;
				$prVariantKeyArr[$tempKey][] = $_REQUEST['vmax_' . $tempKey];
				$prVariantKeyArr[$tempKey][] = $_REQUEST['vstock_' . $tempKey];
				$prVariantKeyArr[$tempKey][] = $_REQUEST['vsku_' . $tempKey];
				$prVariantKeyArr[$tempKey][] = $_REQUEST['vprice_' . $tempKey];
			}
		}
		
		$instockCnt = 0;
		if(count($prVariantKeyArr) > 0) {
			foreach($prVariantKeyArr AS $key => $value) {
				//if(!empty($value[0]) && !empty($value[1]) && !empty($value[2]) && !empty($value[3])) {
					$aidsStr = '';
					$aidsArr = array();
					$avidsStr = '';
					$avidsArr = array();
					$tempAidsAvidsArr = array();
					$tempAidsAvidsArr = explode('_', $key);
					foreach($tempAidsAvidsArr AS $value2) {
						$temp2AidsAvidsArr = array();
						$temp2AidsAvidsArr = explode("-", $value2);
						$aidsArr[] = $temp2AidsAvidsArr[0];
						$avidsArr[] = $temp2AidsAvidsArr[1];
					}
					$aidsStr = implode(',', $aidsArr); 
					$avidsStr = implode(',', $avidsArr);

					if(empty($value[4])) {
						$value[4] = '0.00';
					}
					
					$qry = "INSERT INTO site_productvariants
							(vid, pid, aids, avids, minqty, maxqty, instock, vsku, vprice)
							VALUES('{$vname}', '{$pid}', '{$aidsStr}', '{$avidsStr}', '{$value[0]}', '{$value[1]}', '{$value[2]}', '{$value[3]}', '{$value[4]}')"; 
					$this->db->query($qry);
					$instockCnt += $value[2];
				//}
			}	
		}
		
		$qry = "UPDATE site_products
					SET in_stock='{$instockCnt}'
					WHERE id='{$pid}'";
		$this->db->query($qry);
		
		$sefurl = htmlentities($_REQUEST['sefurl'], ENT_QUOTES);

		$qry = "INSERT INTO site_manager
				(sefurl, realurl, application, published)
				VALUES ('$sefurl', 'index.php?app=app_shop&pid=$pid', 'app_shop', 'Y')";
		$this->db->query($qry);

		/*Images*/
		$imageArr = array();
		$imageStr = '';
		$image = '';
		$imagePath = ROOT . DS . 'applications' . DS . 'app_shop' . DS . 'images' . DS . 'products' . DS . 'large'; 
		$mediumPath = ROOT . DS . 'applications' . DS . 'app_shop' . DS . 'images' . DS . 'products' . DS . 'medium';
		$smallPath = ROOT . DS . 'applications' . DS . 'app_shop' . DS . 'images' . DS . 'products' . DS . 'small';
		$thumbPath = ROOT . DS . 'applications' . DS . 'app_shop' . DS . 'images' . DS . 'products' . DS . 'thumbnail';

		$time = md5(microtime().rand(0,999999));
			
		if(count($_FILES) > 0) {
			$flag = 0;
			foreach($_FILES AS $key => $value) {
				if($_FILES[$key]["error"] == 0) { 
					if ((($_FILES[$key]["type"] == "image/gif") || ($_FILES[$key]["type"] == "image/jpeg") || ($_FILES[$key]["type"] == "image/pjpeg"))) {
						/*Default Values*/
						$largeImageWidth = 800;
						$largeImageHeight = 600;
						$mediumImageWidth = 400;
						$mediumImageHeight = 300;
						$smallImageWidth = 100;
						$smallImageHeight = 100;
						$thumbImageWidth = 50;
						$thumbimageHeight = 50;

						$size = getimagesize($_FILES[$key]["tmp_name"]);	

						$ratiowidth = round($size[1] / $size[0], 2);
						$ratioheight = round($size[0] / $size[1], 2);
						
						/*Large Image Width Height*/
						switch($this->product_config->product_large_image_flag) {
							case 'width':
								if(!empty($this->product_config->product_large_image_width)) {
									$largeImageWidth = $this->product_config->product_large_image_width;
									$largeImageHeight = round($largeImageWidth * $ratiowidth); 
								}
								break;
							case 'height':
								if(!empty($this->product_config->product_large_image_height)) {
									$largeImageHeight = $this->product_config->product_large_image_height; 
									$largeImageWidth = round($largeImageHeight * $ratioheight);
								}
								break;
						}

						/*Medium Image Width Height*/
						switch($this->product_config->product_medium_image_flag) {
							case 'width':
								if(!empty($this->product_config->product_medium_image_width)) {
									$mediumImageWidth = $this->product_config->product_medium_image_width;
									$mediumImageHeight = round($mediumImageWidth * $ratiowidth);
								}
								break;
							case 'height':
								if(!empty($this->product_config->product_medium_image_height)) {
									$mediumImageHeight = $this->product_config->product_medium_image_height;
									$mediumImageWidth = round($mediumImageHeight * $ratioheight);
								}
								break;
						}

						/*Small Image Width Height*/
						switch($this->product_config->product_small_image_flag) {
							case 'width':
								if(!empty($this->product_config->product_small_image_width)) {
									$smallImageWidth = $this->product_config->product_small_image_width;
									$smallImageHeight = round($smallImageWidth * $ratiowidth);
								}	
								break;
							case 'height':
								if(!empty($this->product_config->product_small_image_height)) {
									$smallImageHeight = $this->product_config->product_small_image_height;
									$smallImageWidth = round($smallImageHeight * $ratioheight);
								}
								break;
						}

						/*Thumbnail Image Width Height*/
						switch($this->product_config->product_thumb_image_flag) {
							case 'width':
								if(!empty($this->product_config->product_thumb_image_width)) {
									$thumbImageWidth = $this->product_config->product_thumb_image_width;
									$thumbimageHeight = round($thumbImageWidth * $ratiowidth);
								}
								break;
							case 'height':
								if(!empty($this->product_config->product_thumb_image_height)) {
									$thumbimageHeight = $this->product_config->product_thumb_image_height;
									$thumbImageWidth = round($thumbimageHeight * $ratioheight);
								}	
								break;
						}

						$imagename = $time . $_FILES[$key]["name"];
						$image = $_FILES[$key]["tmp_name"];
						$this->createImage($imagename, $image, $imagePath, "large-", $largeImageWidth, $largeImageHeight, 100);
						$this->createImage($imagename, $image, $mediumPath, "medium-", $mediumImageWidth, $mediumImageHeight, 100);
						$this->createImage($imagename, $image, $smallPath, "small-", $smallImageWidth, $smallImageHeight, 100);
						$this->createImage($imagename, $image, $thumbPath, "thumb-", $thumbImageWidth, $thumbimageHeight, 100);
						
						$tempImgCnt = str_replace('file', '', $key);
						$imageArr[$tempImgCnt] = $imagename;
						if(isset($_REQUEST['defaultimage'])) {
							if($_REQUEST['defaultimage'] == $tempImgCnt) {
								$flag = $tempImgCnt;
							}
						}
					}
				}
			}
			
			$imageArrDefaultFirstArr = array();
			foreach($imageArr AS $key => $value) {
				if($key == $flag) {
					$imageArrDefaultFirstArr[] = $value;
				}
			}
			
			unset($imageArr[$flag]);
			foreach($imageArr AS $key => $value) {
				$imageArrDefaultFirstArr[] = $value;
			}
			$imageStr = implode('|', $imageArrDefaultFirstArr);
		}

		if(!empty($imageStr)) {
			$qry = "UPDATE site_products
					SET image='{$imageStr}'
					WHERE id='{$pid}'";
			$this->db->query($qry);
		}
	}

	/*Create thumbnails*/
	function createImage($imagename, $img, $thumbPath, $suffix, $newWidth, $newHeight, $quality) {
		// Open the original image.
		$original = imagecreatefromjpeg($img) or die("Error Opening original");
		list($width, $height, $type, $attr) = getimagesize($img);
 
		// Resample the image.
		$tempImg = imagecreatetruecolor($newWidth, $newHeight) or die("Cant create temp image");
		imagecopyresized($tempImg, $original, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height) or die("Cant resize copy");
 
		// Create the new file name.
		
		$newNameE = explode(".", $imagename);
		$newName = ''. $suffix .''. $newNameE[0] .'.'. $newNameE[1] .''; 
		
 
		// Save the image.
		imagejpeg($tempImg, $thumbPath . DS . $newName, $quality) or die("Cant save image");
 
		// Clean up.
		imagedestroy($original);
		imagedestroy($tempImg);
	}

	//Update to table
	function editsave() {
		$name = htmlentities($_REQUEST['name'], ENT_QUOTES);
		//$vname = $this->adminId;
		$parentTempArr = array();
		$parentStr = '';
		if(isset($_REQUEST['parent'])) {
			foreach($_REQUEST['parent'] AS $key => $value) {
				$parentTempArr[] = htmlentities($value, ENT_QUOTES);
			}
			$parentStr = implode(',', $parentTempArr);
		}
		$code = htmlentities($_REQUEST['code'], ENT_QUOTES); 
		$sku = htmlentities($_REQUEST['sku'], ENT_QUOTES);
		$price = htmlentities($_REQUEST['price'], ENT_QUOTES);
		$dprice = htmlentities($_REQUEST['dprice'], ENT_QUOTES);
		$published = $_REQUEST['published'];
		$short_desc = htmlentities($_REQUEST['short_desc'], ENT_QUOTES);
		$description = $_REQUEST['description'];
		$in_stock = htmlentities($_REQUEST['in_stock'], ENT_QUOTES);
		$min_purchase_qty = htmlentities($_REQUEST['min_purchase_qty'], ENT_QUOTES);
		$max_purchase_qty = htmlentities($_REQUEST['max_purchase_qty'], ENT_QUOTES);
		$available_dateTemp = htmlentities($_REQUEST['available_date'], ENT_QUOTES); 
		$available_dateTemp = explode("-", $available_dateTemp);
		
		/*Brand Id calculation*/
		$vendorbrandId = 0;
		$vendorbrand = htmlentities($_REQUEST['vendorbrand'], ENT_QUOTES);
		if($vendorbrand !== 0) {
			$vendorbrandId = $vendorbrand;
		}
		$qry = "SELECT associated_vids FROM site_vendor_brands
				WHERE id = '{$vendorbrand}'";
		$data = $this->db->query($qry);
		$associatedvidsArr = array();
		while($row = $this->db->fetch($data)) {
			$tempArr = array();
			$tempArr = explode(",", $row['associated_vids']);
			foreach($tempArr AS $value) {
				$associatedvidsArr[] = $value; 
			}
		}
		if(!in_array($this->adminId, $associatedvidsArr)) {
			$associatedvidsArr[] = $this->adminId;
			$associatedvidsStr = '';
			$associatedvidsStr = implode(",", $associatedvidsArr);
			$qry = "UPDATE site_vendor_brands
					SET associated_vids = '{$associatedvidsStr}'
					WHERE id = '{$vendorbrand}'";
			$this->db->query($qry);	
		}

		/*[edit the front display slider setting][vimal_chauhan][13-Apr-2011][start] */
		$latest_sale_flag = 'N';
		$fresh_arrivals_flag = 'N';
		$featured_products_flag = 'N';
		$best_sellers_flag = 'N';
		$off_pricers_flag = 'N';
		
		if(isset($_REQUEST['latest_sale_flag'])) {
			$latest_sale_flag = $_REQUEST['latest_sale_flag'];
		}
		if(isset($_REQUEST['fresh_arrivals_flag'])) {
			$fresh_arrivals_flag = $_REQUEST['fresh_arrivals_flag'];
		}
		if(isset($_REQUEST['featured_products_flag'])) {
			$featured_products_flag = $_REQUEST['featured_products_flag'];
		}
		if(isset($_REQUEST['best_sellers_flag'])) {
			$best_sellers_flag = $_REQUEST['best_sellers_flag'];
		}
		if(isset($_REQUEST['off_pricers_flag'])) {
			$off_pricers_flag = $_REQUEST['off_pricers_flag'];
		}
		/*[edit the front display slider setting][vimal_chauhan][13-Apr-2011][end] */
		$available_date = '';
		if(isset($available_dateTemp[2]) && isset($available_dateTemp[1]) && isset($available_dateTemp[0])) {
			$available_dateTemp = $available_dateTemp[2] . "-" . $available_dateTemp[1] . "-" . $available_dateTemp[0];
		}
		if($available_dateTemp == "--") {
			$available_dateTemp = '';
		}
		$available_date = $available_dateTemp;
		$id = htmlentities($_REQUEST['pid'], ENT_QUOTES);

		$related_products = '';
		$related_productsStr = '';
		if(isset($_REQUEST['related_products'])) {
			$related_productsStr = implode(',', $_REQUEST['related_products']);
			$related_products = $related_productsStr;
		}
		/*[edit the front display slider setting][vimal_chauhan][13-Apr-2011][start] */
		$qry = "UPDATE site_products
				SET category_id = '{$parentStr}',
				brandid = '{$vendorbrandId}', 
				variant = 'N',
				code = '{$code}',
				sku = '{$sku}',
				short_desc = '{$short_desc}',
				long_desc = '{$description}',
				price = '{$price}',
				dprice = '{$dprice}',
				in_stock = '{$in_stock}',
				available_date = '{$available_date}',
				name = '{$name}',
				min_purchase_qty = '{$min_purchase_qty}',
				max_purchase_qty = '{$max_purchase_qty}',
				related_products = '{$related_products}',
				published = '{$published}',
				latest_sale_flag = '{$latest_sale_flag}',
				fresh_arrivals_flag = '{$fresh_arrivals_flag}',
				featured_products_flag = '{$featured_products_flag}',
				best_sellers_flag = '{$best_sellers_flag}',
				off_pricers_flag = '{$off_pricers_flag}'
			WHERE id = '{$id}'";
		/*[edit the front display slider setting][vimal_chauhan][13-Apr-2011][end] */
		$this->db->query($qry);

		$sefurl = htmlentities($_REQUEST['sefurl'], ENT_QUOTES);
		
		$qry = "UPDATE site_manager
				SET sefurl='{$sefurl}'
				WHERE realurl = 'index.php?app=app_shop&pid=" . $id . "'";

		$this->db->query($qry);

		/*Images*/
		$imageArr = array();
		$imageStr = '';
		$image = '';
		$imagePath = ROOT . DS . 'applications' . DS . 'app_shop' . DS . 'images' . DS . 'products' . DS . 'large'; 
		$mediumPath = ROOT . DS . 'applications' . DS . 'app_shop' . DS . 'images' . DS . 'products' . DS . 'medium';
		$smallPath = ROOT . DS . 'applications' . DS . 'app_shop' . DS . 'images' . DS . 'products' . DS . 'small';
		$thumbPath = ROOT . DS . 'applications' . DS . 'app_shop' . DS . 'images' . DS . 'products' . DS . 'thumbnail';

		$time = md5(microtime().rand(0,999999));
			
		if(count($_FILES) > 0) {
			$flag = 0;
			foreach($_FILES AS $key => $value) {
				if($_FILES[$key]["error"] == 0) { 
					if ((($_FILES[$key]["type"] == "image/gif") || ($_FILES[$key]["type"] == "image/jpeg") || ($_FILES[$key]["type"] == "image/pjpeg"))) {
						/*Default Values*/
						$largeImageWidth = 800;
						$largeImageHeight = 600;
						$mediumImageWidth = 400;
						$mediumImageHeight = 300;
						$smallImageWidth = 100;
						$smallImageHeight = 100;
						$thumbImageWidth = 50;
						$thumbimageHeight = 50;

						$size = getimagesize($_FILES[$key]["tmp_name"]);	

						$ratiowidth = round($size[1] / $size[0], 2);
						$ratioheight = round($size[0] / $size[1], 2);
						
						/*Large Image Width Height*/
						switch($this->product_config->product_large_image_flag) {
							case 'width':
								if(!empty($this->product_config->product_large_image_width)) {
									$largeImageWidth = $this->product_config->product_large_image_width;
									$largeImageHeight = round($largeImageWidth * $ratiowidth); 
								}
								break;
							case 'height':
								if(!empty($this->product_config->product_large_image_height)) {
									$largeImageHeight = $this->product_config->product_large_image_height; 
									$largeImageWidth = round($largeImageHeight * $ratioheight);
								}
								break;
						}

						/*Medium Image Width Height*/
						switch($this->product_config->product_medium_image_flag) {
							case 'width':
								if(!empty($this->product_config->product_medium_image_width)) {
									$mediumImageWidth = $this->product_config->product_medium_image_width;
									$mediumImageHeight = round($mediumImageWidth * $ratiowidth);
								}
								break;
							case 'height':
								if(!empty($this->product_config->product_medium_image_height)) {
									$mediumImageHeight = $this->product_config->product_medium_image_height;
									$mediumImageWidth = round($mediumImageHeight * $ratioheight);
								}
								break;
						}

						/*Small Image Width Height*/
						switch($this->product_config->product_small_image_flag) {
							case 'width':
								if(!empty($this->product_config->product_small_image_width)) {
									$smallImageWidth = $this->product_config->product_small_image_width;
									$smallImageHeight = round($smallImageWidth * $ratiowidth);
								}	
								break;
							case 'height':
								if(!empty($this->product_config->product_small_image_height)) {
									$smallImageHeight = $this->product_config->product_small_image_height;
									$smallImageWidth = round($smallImageHeight * $ratioheight);
								}
								break;
						}

						/*Thumbnail Image Width Height*/
						switch($this->product_config->product_thumb_image_flag) {
							case 'width':
								if(!empty($this->product_config->product_thumb_image_width)) {
									$thumbImageWidth = $this->product_config->product_thumb_image_width;
									$thumbimageHeight = round($thumbImageWidth * $ratiowidth);
								}
								break;
							case 'height':
								if(!empty($this->product_config->product_thumb_image_height)) {
									$thumbimageHeight = $this->product_config->product_thumb_image_height;
									$thumbImageWidth = round($thumbimageHeight * $ratioheight);
								}	
								break;
						}

						$imagename = htmlentities($time . $_FILES[$key]["name"], ENT_QUOTES);
						$image = $_FILES[$key]["tmp_name"];
						$this->createImage($imagename, $image, $imagePath, "large-", $largeImageWidth, $largeImageHeight, 100);
						$this->createImage($imagename, $image, $mediumPath, "medium-", $mediumImageWidth, $mediumImageHeight, 100);
						$this->createImage($imagename, $image, $smallPath, "small-", $smallImageWidth, $smallImageHeight, 100);
						$this->createImage($imagename, $image, $thumbPath, "thumb-", $thumbImageWidth, $thumbimageHeight, 100);
						
						$tempImgCnt = str_replace('file', '', $key);
						$imageArr[$tempImgCnt] = $imagename;
						if(isset($_REQUEST['defaultimage'])) {
							if($_REQUEST['defaultimage'] == $tempImgCnt) {
								$flag = $tempImgCnt;
							}
						}
					}
				}
			}
			/*echo $flag; exit;
			if($flag !== 0) {
				$flag = $flag - 1;
			}*/
			
			$imageArrDefaultFirstArr = array();
			foreach($imageArr AS $key => $value) {
				if($key == $flag) {
					$imageArrDefaultFirstArr[] = $value;
				}
			}
			
			unset($imageArr[$flag]);
			foreach($imageArr AS $key => $value) {
				$imageArrDefaultFirstArr[] = $value;
			}
			$imageStr = implode('|', $imageArrDefaultFirstArr);
		}
		
		$setExistingImagesFirstFlag = 0;
		$tempimageStr = '';
		$tempimageArr = array(); 
		$imageQry = "SELECT image FROM site_products
						WHERE id='{$id}'";
		
		$data = $this->db->query($imageQry);

		while($result = $this->db->fetch($data)) {
			$tempimageStr = $result['image'];
		}

		if(!empty($tempimageStr)) {
			$tempimageArr = explode("|", $tempimageStr);
			
			if(count($tempimageArr) > 0) {
				$existingImageArrDefaultFirstArr = array();
				$flagImage = 0;
				if(isset($_REQUEST['defaultimage'])) {
					$flagImage = $_REQUEST['defaultimage'] - 1;
					foreach($tempimageArr AS $key => $value) {
						if($flagImage == $key) {
							$existingImageArrDefaultFirstArr[] = $value;  
							$setExistingImagesFirstFlag = 1;
						}
					}
					unset($tempimageArr[$flagImage]);

					foreach($tempimageArr AS $key => $value) {
						$existingImageArrDefaultFirstArr[] = $value;
					}
					$tempimageStr = implode('|', $existingImageArrDefaultFirstArr);
				}
				
			}
		}
		
		if(!empty($tempimageStr)) {
			if($setExistingImagesFirstFlag == 1) {
				if(!empty($imageStr)) {
					$imageStr = $tempimageStr . "|" . $imageStr;		
				}
				else {
					$imageStr = $tempimageStr;
				}
			}
			else {
				if(!empty($imageStr)) {
					$imageStr = $imageStr . "|" . $tempimageStr; 
				}
				else {
					$imageStr = $tempimageStr;
				}
			}
		}
		

		if(!empty($imageStr)) {
			$qry = "UPDATE site_products
					SET image='{$imageStr}'
					WHERE id='{$id}'"; 
			$this->db->query($qry); 
		}
	}

	function editsaveattributeproduct() {
		$name = htmlentities($_REQUEST['name'], ENT_QUOTES);
		//$vname = $this->adminId;
		$parentTempArr = array();
		$parentStr = '';
		if(isset($_REQUEST['parent'])) {
			foreach($_REQUEST['parent'] AS $key => $value) {
				$parentTempArr[] = htmlentities($value, ENT_QUOTES);
			}
			$parentStr = implode(',', $parentTempArr);
		}
		$code = htmlentities($_REQUEST['code'], ENT_QUOTES);
		$price = htmlentities($_REQUEST['price'], ENT_QUOTES);
		$dprice = htmlentities($_REQUEST['dprice'], ENT_QUOTES);
		$published = $_REQUEST['published'];
		$short_desc = htmlentities($_REQUEST['short_desc'], ENT_QUOTES);
		$description = $_REQUEST['description'];
		/*$min_purchase_qty = htmlentities($_REQUEST['min_purchase_qty'], ENT_QUOTES);
		$max_purchase_qty = htmlentities($_REQUEST['max_purchase_qty'], ENT_QUOTES);*/
		$available_dateTemp = htmlentities($_REQUEST['available_date'], ENT_QUOTES); 
		$available_dateTemp = explode("-", $available_dateTemp);
		$available_date = '';
		
		/*Brand Id calculation*/
		$vendorbrandId = 0;
		$vendorbrand = htmlentities($_REQUEST['vendorbrand'], ENT_QUOTES);
		if($vendorbrand !== 0) {
			$vendorbrandId = $vendorbrand;
		}
		$qry = "SELECT associated_vids FROM site_vendor_brands
				WHERE id = '{$vendorbrand}'";
		$data = $this->db->query($qry);
		$associatedvidsArr = array();
		while($row = $this->db->fetch($data)) {
			$tempArr = array();
			$tempArr = explode(",", $row['associated_vids']);
			foreach($tempArr AS $value) {
				$associatedvidsArr[] = $value; 
			}
		}
		if(!in_array($this->adminId, $associatedvidsArr)) {
			$associatedvidsArr[] = $this->adminId;
			$associatedvidsStr = '';
			$associatedvidsStr = implode(",", $associatedvidsArr);
			$qry = "UPDATE site_vendor_brands
					SET associated_vids = '{$associatedvidsStr}'
					WHERE id = '{$vendorbrand}'";
			$this->db->query($qry);	
		}

		/*[edit the front display slider setting][vimal_chauhan][13-Apr-2011][start] */
		$latest_sale_flag = 'N';
		$fresh_arrivals_flag = 'N';
		$featured_products_flag = 'N';
		$best_sellers_flag = 'N';
		$off_pricers_flag = 'N';
		
		if(isset($_REQUEST['latest_sale_flag'])) {
			$latest_sale_flag = $_REQUEST['latest_sale_flag'];
		}
		if(isset($_REQUEST['fresh_arrivals_flag'])) {
			$fresh_arrivals_flag = $_REQUEST['fresh_arrivals_flag'];
		}
		if(isset($_REQUEST['featured_products_flag'])) {
			$featured_products_flag = $_REQUEST['featured_products_flag'];
		}
		if(isset($_REQUEST['best_sellers_flag'])) {
			$best_sellers_flag = $_REQUEST['best_sellers_flag'];
		}
		if(isset($_REQUEST['off_pricers_flag'])) {
			$off_pricers_flag = $_REQUEST['off_pricers_flag'];
		}
		/*[edit the front display slider setting][vimal_chauhan][13-Apr-2011][end] */
		
		if(isset($available_dateTemp[2]) && isset($available_dateTemp[1]) && isset($available_dateTemp[0])) {
			$available_dateTemp = $available_dateTemp[2] . "-" . $available_dateTemp[1] . "-" . $available_dateTemp[0];
		}
		if($available_dateTemp == "--") {
			$available_dateTemp = '';
		}
		$available_date = $available_dateTemp;
		$id = htmlentities($_REQUEST['pid'], ENT_QUOTES);

		$related_products = '';
		$related_productsStr = '';
		if(isset($_REQUEST['related_products'])) {
			$related_productsStr = implode(',', $_REQUEST['related_products']);
			$related_products = $related_productsStr;
		}

		$qry = "UPDATE site_products
				SET category_id = '{$parentStr}',
				brandid = '{$vendorbrandId}', 
				variant = 'Y',
				code = '{$code}',
				short_desc = '{$short_desc}',
				long_desc = '{$description}',
				price = '{$price}',
				dprice = '{$dprice}',
				available_date = '{$available_date}',
				name = '{$name}',
				related_products = '{$related_products}',
				published = '{$published}',
				latest_sale_flag = '{$latest_sale_flag}',
				fresh_arrivals_flag = '{$fresh_arrivals_flag}',
				featured_products_flag = '{$featured_products_flag}',
				best_sellers_flag = '{$best_sellers_flag}',
				off_pricers_flag = '{$off_pricers_flag}'
				WHERE id = '{$id}'";
		
		$this->db->query($qry);
		
		$sefurl = htmlentities($_REQUEST['sefurl'], ENT_QUOTES);
		
		$qry = "UPDATE site_manager
				SET sefurl='{$sefurl}'
				WHERE realurl = 'index.php?app=app_shop&pid=" . $id . "'";

		$this->db->query($qry);

		/*Images*/
		$imageArr = array();
		$imageStr = '';
		$image = '';
		$imagePath = ROOT . DS . 'applications' . DS . 'app_shop' . DS . 'images' . DS . 'products' . DS . 'large'; 
		$mediumPath = ROOT . DS . 'applications' . DS . 'app_shop' . DS . 'images' . DS . 'products' . DS . 'medium';
		$smallPath = ROOT . DS . 'applications' . DS . 'app_shop' . DS . 'images' . DS . 'products' . DS . 'small';
		$thumbPath = ROOT . DS . 'applications' . DS . 'app_shop' . DS . 'images' . DS . 'products' . DS . 'thumbnail';

		$time = md5(microtime().rand(0,999999));

		if(count($_FILES) > 0) {
			$flag = 0;
			foreach($_FILES AS $key => $value) {
				if($_FILES[$key]["error"] == 0) { 
					if ((($_FILES[$key]["type"] == "image/gif") || ($_FILES[$key]["type"] == "image/jpeg") || ($_FILES[$key]["type"] == "image/pjpeg"))) {
						/*Default Values*/
						$largeImageWidth = 800;
						$largeImageHeight = 600;
						$mediumImageWidth = 400;
						$mediumImageHeight = 300;
						$smallImageWidth = 100;
						$smallImageHeight = 100;
						$thumbImageWidth = 50;
						$thumbimageHeight = 50;

						$size = getimagesize($_FILES[$key]["tmp_name"]);	

						$ratiowidth = round($size[1] / $size[0], 2);
						$ratioheight = round($size[0] / $size[1], 2);
						
						/*Large Image Width Height*/
						switch($this->product_config->product_large_image_flag) {
							case 'width':
								if(!empty($this->product_config->product_large_image_width)) {
									$largeImageWidth = $this->product_config->product_large_image_width;
									$largeImageHeight = round($largeImageWidth * $ratiowidth); 
								}
								break;
							case 'height':
								if(!empty($this->product_config->product_large_image_height)) {
									$largeImageHeight = $this->product_config->product_large_image_height; 
									$largeImageWidth = round($largeImageHeight * $ratioheight);
								}
								break;
						}

						/*Medium Image Width Height*/
						switch($this->product_config->product_medium_image_flag) {
							case 'width':
								if(!empty($this->product_config->product_medium_image_width)) {
									$mediumImageWidth = $this->product_config->product_medium_image_width;
									$mediumImageHeight = round($mediumImageWidth * $ratiowidth);
								}
								break;
							case 'height':
								if(!empty($this->product_config->product_medium_image_height)) {
									$mediumImageHeight = $this->product_config->product_medium_image_height;
									$mediumImageWidth = round($mediumImageHeight * $ratioheight);
								}
								break;
						}

						/*Small Image Width Height*/
						switch($this->product_config->product_small_image_flag) {
							case 'width':
								if(!empty($this->product_config->product_small_image_width)) {
									$smallImageWidth = $this->product_config->product_small_image_width;
									$smallImageHeight = round($smallImageWidth * $ratiowidth);
								}	
								break;
							case 'height':
								if(!empty($this->product_config->product_small_image_height)) {
									$smallImageHeight = $this->product_config->product_small_image_height;
									$smallImageWidth = round($smallImageHeight * $ratioheight);
								}
								break;
						}

						/*Thumbnail Image Width Height*/
						switch($this->product_config->product_thumb_image_flag) {
							case 'width':
								if(!empty($this->product_config->product_thumb_image_width)) {
									$thumbImageWidth = $this->product_config->product_thumb_image_width;
									$thumbimageHeight = round($thumbImageWidth * $ratiowidth);
								}
								break;
							case 'height':
								if(!empty($this->product_config->product_thumb_image_height)) {
									$thumbimageHeight = $this->product_config->product_thumb_image_height;
									$thumbImageWidth = round($thumbimageHeight * $ratioheight);
								}	
								break;
						}

						$imagename = htmlentities($time . $_FILES[$key]["name"], ENT_QUOTES);
						$image = $_FILES[$key]["tmp_name"];
						$this->createImage($imagename, $image, $imagePath, "large-", $largeImageWidth, $largeImageHeight, 100);
						$this->createImage($imagename, $image, $mediumPath, "medium-", $mediumImageWidth, $mediumImageHeight, 100);
						$this->createImage($imagename, $image, $smallPath, "small-", $smallImageWidth, $smallImageHeight, 100);
						$this->createImage($imagename, $image, $thumbPath, "thumb-", $thumbImageWidth, $thumbimageHeight, 100);
						
						$tempImgCnt = str_replace('file', '', $key);
						$imageArr[$tempImgCnt] = $imagename;
						if(isset($_REQUEST['defaultimage'])) {
							if($_REQUEST['defaultimage'] == $tempImgCnt) {
								$flag = $tempImgCnt;
							}
						}
					}
				}
			}
			/*echo $flag; exit;
			if($flag !== 0) {
				$flag = $flag - 1;
			}*/
			
			$imageArrDefaultFirstArr = array();
			foreach($imageArr AS $key => $value) {
				if($key == $flag) {
					$imageArrDefaultFirstArr[] = $value;
				}
			}
			
			unset($imageArr[$flag]);
			foreach($imageArr AS $key => $value) {
				$imageArrDefaultFirstArr[] = $value;
			}
			$imageStr = implode('|', $imageArrDefaultFirstArr);
		}

		$setExistingImagesFirstFlag = 0;
		$tempimageStr = '';
		$tempimageArr = array(); 
		$imageQry = "SELECT image FROM site_products
						WHERE id='{$id}'";
		
		$data = $this->db->query($imageQry);

		while($result = $this->db->fetch($data)) {
			$tempimageStr = $result['image'];
		}

		if(!empty($tempimageStr)) {
			$tempimageArr = explode("|", $tempimageStr);
			
			if(count($tempimageArr) > 0) {
				$existingImageArrDefaultFirstArr = array();
				$flagImage = 0;
				if(isset($_REQUEST['defaultimage'])) {
					$flagImage = $_REQUEST['defaultimage'] - 1;
					foreach($tempimageArr AS $key => $value) {
						if($flagImage == $key) {
							$existingImageArrDefaultFirstArr[] = $value;  
							$setExistingImagesFirstFlag = 1;
						}
					}
					unset($tempimageArr[$flagImage]);

					foreach($tempimageArr AS $key => $value) {
						$existingImageArrDefaultFirstArr[] = $value;
					}
					$tempimageStr = implode('|', $existingImageArrDefaultFirstArr);
				}
				
			}
		}

		if(!empty($tempimageStr)) {
			if($setExistingImagesFirstFlag == 1) {
				if(!empty($imageStr)) {
					$imageStr = $tempimageStr . "|" . $imageStr;		
				}
				else {
					$imageStr = $tempimageStr;
				}
			}
			else {
				if(!empty($imageStr)) {
					$imageStr = $imageStr . "|" . $tempimageStr; 
				}
				else {
					$imageStr = $tempimageStr;
				}
			}
		}

		if(!empty($imageStr)) {
			$qry = "UPDATE site_products
					SET image='{$imageStr}'
					WHERE id='{$id}'"; 
			$this->db->query($qry); 
		}

		/*Variant products*/
		$qry = "DELETE FROM site_productvariants 
							WHERE pid = '{$id}'";
		$this->db->query($qry); 

		$prVariantKeyArr = array();
		foreach($_REQUEST AS $key => $value) {
			if(substr($key, 0, 5) == 'vmin_') {
				$tempKey = str_replace('vmin_', '', $key);
				$prVariantKeyArr[$tempKey] = array();
				$prVariantKeyArr[$tempKey][] = $value;
				$prVariantKeyArr[$tempKey][] = $_REQUEST['vmax_' . $tempKey];
				$prVariantKeyArr[$tempKey][] = $_REQUEST['vstock_' . $tempKey];
				$prVariantKeyArr[$tempKey][] = $_REQUEST['vsku_' . $tempKey];
				$prVariantKeyArr[$tempKey][] = $_REQUEST['vprice_' . $tempKey];
			}
		}
		
		$instockCnt = 0;
		if(count($prVariantKeyArr) > 0) {
			foreach($prVariantKeyArr AS $key => $value) {
				//if(!empty($value[0]) && !empty($value[1]) && !empty($value[2]) && !empty($value[3])) {
					$aidsStr = '';
					$aidsArr = array();
					$avidsStr = '';
					$avidsArr = array();
					$tempAidsAvidsArr = array();
					$tempAidsAvidsArr = explode('_', $key);
					foreach($tempAidsAvidsArr AS $value2) {
						$temp2AidsAvidsArr = array();
						$temp2AidsAvidsArr = explode("-", $value2);
						$aidsArr[] = $temp2AidsAvidsArr[0];
						$avidsArr[] = $temp2AidsAvidsArr[1];
					}
					$aidsStr = implode(',', $aidsArr); 
					$avidsStr = implode(',', $avidsArr);
					$vendorId = $_REQUEST['vendorId'];

					if(empty($value[4])) {
						$value[4] = '0.00';
					}

					$qry = "INSERT INTO site_productvariants
							(vid, pid, aids, avids, minqty, maxqty, instock, vsku, vprice)
							VALUES('{$vendorId}', '{$id}', '{$aidsStr}', '{$avidsStr}', '{$value[0]}', '{$value[1]}', '{$value[2]}', '{$value[3]}', '{$value[4]}')"; 
					$this->db->query($qry);
					$instockCnt += $value[2];
				//}
			}	
		}
		
		$qry = "UPDATE site_products
					SET in_stock='{$instockCnt}'
					WHERE id='{$id}'";
		$this->db->query($qry);
		
		
	}

	//Delete records
	function deleteids() {
		$deleteString = $_REQUEST['deleteString'];
		$deleteString = substr($deleteString, 0, -1); 
		$deleteArray = explode('_', $deleteString);
		
		$flagDelete = 0;
		foreach($deleteArray AS $value) {
			$imgQry = "SELECT image FROM site_products
						WHERE id = '{$value}'";
			$data = $this->db->query($imgQry);
			$image = '';
			while($row = $this->db->fetch($data)) {
				$image = $row['image'];
			}
			$imagePath = ROOT . DS . 'applications' . DS . 'app_shop' . DS . 'images' . DS . 'products' . DS . 'large'; 
			$mediumPath = ROOT . DS . 'applications' . DS . 'app_shop' . DS . 'images' . DS . 'products' . DS . 'medium';
			$smallPath = ROOT . DS . 'applications' . DS . 'app_shop' . DS . 'images' . DS . 'products' . DS . 'small';
			$thumbPath = ROOT . DS . 'applications' . DS . 'app_shop' . DS . 'images' . DS . 'products' . DS . 'thumbnail';
			if(!empty($image)) {
				$imageArr = array();
				$imageArr = explode('|', $image);
				foreach($imageArr AS $value2) {
					unlink($imagePath . DS . 'large-' . $value2);
					unlink($mediumPath . DS . 'medium-' . $value2);
					unlink($smallPath . DS . 'small-' . $value2);
					unlink($thumbPath . DS . 'thumb-' . $value2);
				}
			}
			$qry = "DELETE FROM site_products 
							WHERE id = '{$value}'";
			$this->db->query($qry); 

			$qry = "DELETE FROM site_productvariants 
							WHERE pid = '{$value}'";
			$this->db->query($qry);
			
			$qry = "DELETE FROM site_manager 
							WHERE realurl = 'index.php?app=app_shop&pid={$value}'";
			$this->db->query($qry); 
		}
	}

	//Delete Image
	function deleteimage() {
		ob_clean();
		$id = htmlentities($_REQUEST['id'], ENT_QUOTES);
		$index = htmlentities($_REQUEST['index'], ENT_QUOTES);
		$qry = "SELECT image FROM site_products
				WHERE id = '{$id}'";
		$data = $this->db->query($qry);
		$image = '';
		while($row = $this->db->fetch($data)) {
			$image = $row['image'];
		}
		$imageArr = array();
		$imageStr = '';
		if(!empty($image)) {
			$imageArr = explode('|', $image);
			$imagePath = ROOT . DS . 'applications' . DS . 'app_shop' . DS . 'images' . DS . 'products' . DS . 'large'; 
			$mediumPath = ROOT . DS . 'applications' . DS . 'app_shop' . DS . 'images' . DS . 'products' . DS . 'medium';
			$smallPath = ROOT . DS . 'applications' . DS . 'app_shop' . DS . 'images' . DS . 'products' . DS . 'small';
			$thumbPath = ROOT . DS . 'applications' . DS . 'app_shop' . DS . 'images' . DS . 'products' . DS . 'thumbnail';
			unlink($imagePath . DS . 'large-' . $imageArr[$index]);
			unlink($mediumPath . DS . 'medium-' . $imageArr[$index]);
			unlink($smallPath . DS . 'small-' . $imageArr[$index]);
			unlink($thumbPath . DS . 'thumb-' . $imageArr[$index]);
			unset($imageArr[$index]);
			$imageStr = implode('|', $imageArr);
			$qry = "UPDATE site_products 
				SET image='{$imageStr}'
				WHERE id = '{$id}'";
			$this->db->query($qry);
			echo "image deleted";
		}
		exit;
	}

	//Publish records
	function publishids() {
		$publishString = $_REQUEST['publishString'];
		$publishString = substr($publishString, 0, -1); 
		$publishArray = explode('_', $publishString);
		
		foreach($publishArray AS $value) {
			$qry = "UPDATE site_products
							SET published = 'Y'
							WHERE id = '{$value}'";
			$this->db->query($qry); 
		}
	}

	//Unpublish records
	function unpublishids() {
		$unpublishString = $_REQUEST['unpublishString'];
		$unpublishString = substr($unpublishString, 0, -1); 
		$unpublishArray = explode('_', $unpublishString);
		
		foreach($unpublishArray AS $value) {
			$qry = "UPDATE site_products
							SET published = 'N'
							WHERE id = '{$value}'";
			$this->db->query($qry); 
		}
	}

	//Publish
	function publish() {
		$qry = "UPDATE site_products
				SET published = 'Y'
				WHERE id = '{$_REQUEST['id']}'";
		$this->db->query($qry);
	}

	//UnPublish
	function unpublish() {
		$qry = "UPDATE site_products
				SET published = 'N'
				WHERE id = '{$_REQUEST['id']}'";
		$this->db->query($qry);
	}

	//checkHomePage
	function checkHomePage($aid) {
		$qry = "SELECT alias FROM site_manager
				WHERE realurl = 'index.php?app=app_content&aid=" . $aid . "'
				AND application='app_content'";
		
		$data = $this->db->query($qry);
		
		$alias = '';

		while($result = $this->db->fetch($data)) {
			$alias = $result['alias']; 
		}

		return $alias;
	}

	function savesortorder() {
		foreach($_REQUEST AS $key => $value) {
			if(substr($key, 0, 8) == 'orderid_') {
				$tempaid = str_replace('orderid_', '', $key);
				$qry = "UPDATE site_products
							SET ordering = '{$value}'
							WHERE id= '{$tempaid}'"; 
				$this->db->query($qry);
			}
		}
	}

	function getAllProducts($vendor_id, $related_products = '') {
		$productArr = array();
		$qry = "SELECT id, name
				FROM site_products
				WHERE published = 'Y'
				AND vendor_id = '{$vendor_id}'";

		if($_REQUEST['job'] == 'edit' || $_REQUEST['job'] == 'editattributeproduct') {
			$qry .= "AND id != '{$_REQUEST['id']}' ";
		}

		if(!empty($related_products)) {
			$qry .= "AND id IN ({$related_products})"; 
		}

		$data = $this->db->query($qry);
		while($row = $this->db->fetch($data)) {
			$productArr[$row['id']] = $row['name'];
		}
		return $productArr;
	}

	
	function getAttributeList($vendor_id) {
		$qry = "SELECT av.id id, 
						av.vid vid, 
						av.aid aid, 
						a.name name, 
						av.avalue avalue
					FROM site_attributevalues av, site_attributes a
					WHERE av.vid = '{$vendor_id}'
					AND a.vid = '{$vendor_id}'
					AND a.id = av.aid
					ORDER BY av.id";
		
		$data = $this->db->query($qry);
		
		$attributeListArr = array();
		$attributeValueArr = array();

		while($row = $this->db->fetch($data)) {
			$attributeListArr[$row['aid']] = $row['name'];
			$attributeValueArr[$row['id']] = $row['aid'] . "_" . $row['avalue'];
		}
		
		if(!empty($attributeListArr) && !empty($attributeValueArr)) {
			$cnt = 0;
			foreach($attributeListArr AS $key => $value) {
				$cnt++;
				echo "<span id='attributename_" . $cnt . "'>" . $value . "</span>" . ": ";
				echo "<select name='attribute_" . $key . "' id='attribute_" . $cnt . "' class='adminTableSelectSmall'>";
				echo "<option value='0'>-None-</option>";	
				foreach($attributeValueArr AS $key2 => $value2) {
					$tempValueArr = array();
					$tempValueArr = explode("_", $value2);
					if($key == $tempValueArr[0]) {
						echo "<option value='" . $key . '-' . $key2 . "'>" . $tempValueArr[1] . "</option>";
					}
				}
				echo "</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			}
			echo "<input type='button' value='Generate' class='generate' onclick=\"generatevariants('{$cnt}');\"><br />";
		}
		//$this->db->data_seek($data, 0);

	}


	function getAttributeListEdit($vendor_id) {
		
		$cnt = 1;
		$attrText = '';
		$qry = "SELECT * FROM site_productvariants
					WHERE pid = '{$_REQUEST['id']}'
					AND vid = '{$vendor_id}'";
		$data = $this->db->query($qry);
		if($this->db->rows($data) > 0) {
			while($row = $this->db->fetch($data)) {
				$aidsStr = $row['aids'];
				$avidsStr = $row['avids'];
				$aidsArr = array();
				$avidsArr = array();
				$aidsArr = explode(",", $aidsStr);
				$avidsArr = explode(",", $avidsStr);
				$keyArr = array();
				$keyStr = '';
				$attrNameArr = array();
				$attrNameStr = '';
				foreach($aidsArr AS $key => $value) {
					foreach($avidsArr AS $key2 => $value2) {
						if($key == $key2) {
							$qry2 = "SELECT avalue FROM site_attributevalues
										WHERE vid = '{$vendor_id}'
										AND id = '{$value2}'";
							$data2 = $this->db->query($qry2);
							while($row2 = $this->db->fetch($data2)) {
								$attrNameArr[] = $row2['avalue'];
							}
							$keyArr[] = $value . "-" . $value2;
						}
					}
				}
				$keyStr = implode("_", $keyArr);
				$attrNameStr = implode(" &gt;&gt; ", $attrNameArr);

				$attrText .=	"<span id='attBox_" . $cnt . "'>";
				$attrText .=		"<br /><br />";
				$attrText .=		$attrNameStr;
				$attrText .=		"&nbsp;&nbsp;&nbsp;";
				$attrText .=		"Min&nbsp;:&nbsp;";
				$attrText .=		"<input type='text' class='adminTableInputTextNoSKU' id='vmin_" . $keyStr . "' name='vmin_" . $keyStr . "' value='" . $row['minqty'] . "'>"; 
				$attrText .=		"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				$attrText .=		"Max&nbsp;:&nbsp;";
				$attrText .=		"<input type='text' class='adminTableInputTextNoSKU' id='vmax_" . $keyStr . "' name='vmax_" . $keyStr . "' value='" . $row['maxqty'] . "'>"; 
				$attrText .=		"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				$attrText .=		"In Stock&nbsp;:&nbsp;";
				$attrText .=		"<input type='text' class='adminTableInputTextNoSKU' id='vstock_" . $keyStr . "' name='vstock_" . $keyStr . "' value='" . $row['instock'] . "'>"; 
				$attrText .=		"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				$attrText .=		"SKU&nbsp;:&nbsp;";
				$attrText .=		"<input type='text' class='adminTableInputTextNoSKU' id='vsku_" . $keyStr . "' name='vsku_" . $keyStr . "' value='" . $row['vsku'] . "'>";
				$attrText .=		"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				$attrText .=		"Price&nbsp;:&nbsp;";
				$attrText .=		"<input type='text' class='adminTableInputTextNoSKU' id='vprice_" . $keyStr . "' name='vprice_" . $keyStr . "' value='" . $row['vprice'] . "'>";
				$attrText .=		"<a class='addnewimage1' onclick=\"removegeneratedvariant('attBox_" . $cnt . "');\" href=\"javascript:void(0);\">Remove</a>";
				$attrText .=	"</span>";
				$cnt++;

				/*<span id="attBox_1">
					<br><br>
					Red &gt;&gt; Small &gt;&gt; Cotton&nbsp;&nbsp;&nbsp;
					Min&nbsp;:&nbsp;<input type='text' class='adminTableInputTextNoSKU' id="vmin_1-1_2-4_3-7" name="vmin_1-1_2-4_3-7">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					Max&nbsp;:&nbsp;<input type="text" class="adminTableInputTextNoSKU" id="vmax_1-1_2-4_3-7" name="vmax_1-1_2-4_3-7">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					In Stock&nbsp;:&nbsp;<input type="text" class="adminTableInputTextNoSKU" id="vstock_1-1_2-4_3-7" name="vstock_1-1_2-4_3-7">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					SKU&nbsp;:&nbsp;<input type="text" class="adminTableInputTextNoSKU" id="vsku_1-1_2-4_3-7" name="vsku_1-1_2-4_3-7"><a class='addnewimage1' onclick="removegeneratedvariant('attBox_1');" href="javascript:void(0);">Remove</a>
				</span>*/
			}
		}
		return $cnt . "||" . $attrText;
	}


	function getAllVendors() {
		$vendorArr = array();
		$qry = "SELECT id, title, f_name, l_name
				FROM site_vendors
				WHERE enable = 'Y'";
		$data = $this->db->query($qry);
		while($row = $this->db->fetch($data)) {
			$vendorArr[$row['id']] = $row['title'] . " " . $row['f_name'] . " " . $row['l_name'];
		}
		return $vendorArr;	
	}
}
?>