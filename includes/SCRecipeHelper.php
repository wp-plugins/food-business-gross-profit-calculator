<?php


class SCRecipeHelper {


	public function __construct() {
		/*AJAX 	prep*/
		add_action('screcipe_action', array($this, 'screcipe_action'));
		add_action('wp_ajax_screcipe_action', array($this, 'screcipe_action'));
		add_action('wp_ajax_nopriv_screcipe_action', array($this, 'screcipe_action'));
	}



	/**
	 * Saves the smarttask data to the post_meta with meta_keys
	 * @todo save data in json format
	 *
	 * @return string echo success for ajax
	 */
	public function screcipe_action() {
		$dataSCRecipe=json_decode(str_replace("\\", "", $_POST['data_screcipe']), true);
		$post_id=$_POST['post_id'];
		$request=$_POST['request'];
		$post=get_post($post_id);
		$access=((get_current_user_id()==$post->post_author || is_admin()) && (get_current_user_id()>0)) ? true : false;

 		if ($request=='get') {
			echo json_encode(array('access'=>$access,'data'=>$this->getDbSCRecipeArray($post_id),'setting'=>json_encode($this->getDbSCRecipeSettingArray($post_id))));
		} else if ($access==true) { //making sure valid user
			if ($request=='add') {
				if ($this->screcipe_add($post_id, $dataSCRecipe)) {
					echo json_encode(array('request'=>$request, 'success'=>true,'setting'=>json_encode($this->getDbSCRecipeSettingArray($post_id))));
				}else {
					echo json_encode(array('request'=>$request, 'success'=>false));
				}
			}else if ($request=='edit') {
				if ($this->screcipe_update($post_id, $dataSCRecipe)) {
					echo json_encode(array('request'=>$request, 'success'=>true,'setting'=>json_encode($this->getDbSCRecipeSettingArray($post_id))));
				}else {
					echo json_encode(array('request'=>$request, 'success'=>false));
				}
			}else if ($request=='remove') {
				if ($this->screcipe_remove($post_id, $dataSCRecipe)) {
					echo json_encode(array('access'=>$access,'data'=>$this->getDbSCRecipeArray($post_id),'setting'=>json_encode($this->getDbSCRecipeSettingArray($post_id))));
				}else {
					echo json_encode(array('request'=>$request, 'success'=>false));
				}
			}else if($request=='update_setting'){
				if($this->updateSCRecipeSetting($post_id, $_POST['data_screcipe'])){
					echo json_encode(array('request'=>$request, 'success'=>true,'setting'=>json_encode($this->getDbSCRecipeSettingArray($post_id))));
				}else{
					echo json_encode(array('request'=>$request, 'success'=>false));
				}
			}
		}

		die(1);

	}

	public function updateSCRecipeSetting($post_id, $dataSCRecipe){
		return update_post_meta($post_id, '_screcipe_setting', $dataSCRecipe);
	}

	public function updateTotalRecipePrices($post_id){
		$totprice=0;
		$arrSCRecipe=$this->getDbSCRecipeArray($post_id);
		foreach ($arrSCRecipe as $SCRecipe) {
			$totprice+=$SCRecipe['price'];
		}
		$settingValue=$this->getDbSCRecipeSettingArray($post_id);
		if(sizeof($settingValue)==0){//default
			$settingValue['total_serving']=0;
			$settingValue['currency']='$';
			$settingValue['gross_percent']=20;
			$settingValue['operating_expenses']=0;
			$settingValue['total_prices']=0;
		}
		$settingValue['total_prices']=$totprice;

		return update_post_meta($post_id, '_screcipe_setting', json_encode($settingValue));
	}

	public function getDbSCRecipeArray($post_id) {
		$oldJSONValue=get_post_meta( $post_id, '_screcipe', true);
		if ($oldJSONValue && $oldJSONValue!='null') {
			return json_decode($oldJSONValue, true);
		}else {
			return array();
		}
	}

	public function getDbSCRecipeSettingArray($post_id) {
		$oldJSONValue=get_post_meta( $post_id, '_screcipe_setting', true);
		if ($oldJSONValue && $oldJSONValue!='null') {
			return json_decode($oldJSONValue, true);
		}else {
			return array();
		}
	}

	public function screcipe_add($post_id, $jsonData) {
		$arrayCollection = $this->getDbSCRecipeArray($post_id);
		array_push($arrayCollection, $jsonData);
		if(update_post_meta($post_id, '_screcipe', json_encode($arrayCollection))){
			$updateSettings=$this->updateTotalRecipePrices($post_id);
			if($updateSettings>0 || $updateSettings){
				return true;
			}
		}
		return false;
	}

	public function screcipe_update($post_id, $jsonData) {
		$arrayCollection = array();
		$arrSCRecipe=$this->getDbSCRecipeArray($post_id);

		foreach ($arrSCRecipe as $SCRecipe) {
			if ($SCRecipe['id'] == $jsonData['id']) {
				$SCRecipe['item']=$jsonData['item'];
				$SCRecipe['measurement']=$jsonData['measurement'];
				$SCRecipe['price']=$jsonData['price'];
			}
			array_push($arrayCollection, $SCRecipe);
		}
		if(update_post_meta($post_id, '_screcipe', json_encode($arrayCollection))){
			$updateSettings=$this->updateTotalRecipePrices($post_id);
			if($updateSettings>0 || $updateSettings){
				return true;
			}
		}
		return false;

	}

	public function screcipe_remove($post_id, $jsonData) {  $arrayCollection = array();
		$arrSCRecipe=$this->getDbSCRecipeArray($post_id);

		foreach ($arrSCRecipe as $SCRecipe) {
			if ($SCRecipe['id'] != $jsonData['id']) {
				array_push($arrayCollection, $SCRecipe);
			}
		}
		if(update_post_meta($post_id, '_screcipe', json_encode($arrayCollection))){
			$updateSettings=$this->updateTotalRecipePrices($post_id);
			if($updateSettings>0 || $updateSettings){
				return true;
			}
		}
		return false;
	}

}