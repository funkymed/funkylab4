<?php
/**
 * TUser - Passerelle vers la table user
 * 
 * @package application
 * @subpackage models
 */
class TUser extends Zend_Db_Table_Abstract
{
	
	protected $_name         	= 'b2_users';
    protected $_primary      	= 'user_id';
    protected $_dependentTables = array('Thread','TMessage','TSubject');
    
	/**
     * Informations de l'utilisateur
     *
     * @var object
     */
    private $userInfo = null;
    
    /**
     * Delog l'utilisateur
     *
     * @return void
     */
    public function delog()
    {
		setCookie("user",null,-3600,"/");
	}
	
    /**
     * Login pour l'utilisateur
     *
     * @param string $pseudo
     * @param string $password
     * @return boolean
     */
	public function login($pseudo = null, $password = null)
	{
		if($pseudo!=null && $password!=null && trim($pseudo)!="" && trim($password)!=""){
			
			$select = $this->getDefaultAdapter()->select()
								                       ->from('b2_users','user_id')
								                       ->where("user_pseudo=?",$pseudo)
								                       ->where("user_password=?",$password)
								                       ->limit(1);
			$stmt = $select -> query();
	 		$result = $stmt -> fetchAll(Zend_Db::FETCH_CLASS);
	 		if(count($result)>0){
 				$row = $result[0];
				$temps = 365*24*3600;  // 1 an
				setCookie("user", $row->user_id,time()+$temps,"/");
				$this->userInfo   = $row;
				return true;
			}else{
				$this->userInfo = $this->getsession();
				return false;
			}
		}
	}
	
    /**
     * Recuperation des données de l'utilisateur
     *
     * @return object
     */
	public function getUserInfo()
	{
		return $this->userInfo;
	}
	
	/**
     * Recuperation de la session
     *
     * @return object
     */
	private function getsession()
	{	
		if(isset($_COOKIE['user'])){
			$this->userInfo=$this->getuserbyid($_COOKIE['user']);
			
		}else{
			$this->userInfo=null;
		}	
	}
	
	/**
     * Recuperation d'un utilisateur par son id
     *
     * @param integer $id
     * @return object
     */
	public function getuserbyid($id = null){
		$stmt = $this->_db->query("SELECT * FROM b2_users WHERE user_id='".$id."' LIMIT 1");
		$rows = $stmt -> fetchAll(Zend_Db::FETCH_CLASS);
		if(count($rows)>0) {
			$row = $rows[0];
			return $row;
		}else{
			return false;
		}
	}
	
	/**
     * Recuperation d'un utilisateur par son id
     *
     * @param integer $id
     * @return object
     */
	function getTopUsers(){
		$users=array();
	 	$query = "SELECT count(b2_messages.id_user_fk) as nb,b2_users.user_pseudo FROM b2_users ";
	 	$query.= "LEFT JOIN b2_messages ON (b2_users.user_id=b2_messages.id_user_fk) ";
	 	$query.= "GROUP BY b2_users.user_id ORDER BY nb DESC LIMIT 5";
	 	$stmt = $this->_db->query($query);
	 	$rows = $stmt -> fetchAll(Zend_Db::FETCH_CLASS);
		foreach($rows as $row){
			$users[]=$row;
		}
		
		return $users;
	}
	
	
	/**
     * Ajout de sujet dans un thread
     *
     * @param string $sujet
     * @param string $texte
     * @param integer $thread
     * @param string $pseudo
     * @return object
     */
	public function addSujet($sujet,$texte,$thread,$pseudo=null)
	{
		if($pseudo==null){
			$this->getsession();
			$allField[]="sujet_pseudo"; 	$allQuery[]="'".$this->userInfo->user_pseudo."'";
			$allField[]="id_user_fk"; 		$allQuery[]="'".$this->userInfo->user_id."'";
		}else{
			$allField[]="sujet_pseudo"; 	$allQuery[]="'".$pseudo."'";
			$allField[]="id_user_fk"; 		$allQuery[]="'0'";
		}
		$allField[]="id_thread_fk"; 		$allQuery[]="'".$thread."'";
		$allField[]="sujet_title"; 			$allQuery[]="'".$this->addSlashesCheckMagic($sujet)."'";
		$allField[]="sujet_text"; 			$allQuery[]="'".$this->addSlashesCheckMagic($texte)."'";
		$allField[]="date_ajout"; 			$allQuery[]='now()';
		
		$this->_db->query("INSERT INTO b2_sujets (".implode(", ",$allField).") VALUES (".implode(", ",$allQuery).")");		
	}
	
	/**
     * Ajout de message dans un sujet
     *
     * @param string $sujet
     * @param string $texte
     * @param integer $thread
     * @param string $pseudo
     * @return object
     */
	public function addMessage(/*string*/ $texte, /*int*/ $thread,/*string*/ $pseudo=null)
	{
		if($pseudo==null){
			$this->getsession();
			$allField[]="message_pseudo"; 	$allQuery[]="'".$this->userInfo->user_pseudo."'";
			$allField[]="id_user_fk"; 		$allQuery[]="'".$this->userInfo->user_id."'";
		}else{
			$allField[]="message_pseudo"; 	$allQuery[]="'".$pseudo."'";
			$allField[]="id_user_fk"; 		$allQuery[]="'0'";
		}
		$allField[]="id_sujet_fk"; 			$allQuery[]="'".$thread."'";
		$allField[]="message_text"; 		$allQuery[]="'".$this->addSlashesCheckMagic($texte)."'";
		$allField[]="date_ajout"; 			$allQuery[]='now()';
		
		$this->_db->query("INSERT INTO b2_messages (".implode(", ",$allField).") VALUES (".implode(", ",$allQuery).")");		
	}
	
	/**
     * Protection d'insertion MYSQL
     *
     * @param string $str
     * @return string
     */
	private function addSlashesCheckMagic($str){
		if (!get_magic_quotes_gpc()){
			$str = addslashes($str);
		}
		return $str;
	}
	
    /**
     * Combien de rÃ©servations a fait cet utilisateur ?
     *
     * @param integer $userId
     * @return integer
     */
    public function getReservationsCount($userId)
    {
        $select = $this->_db->select();
        $select->from(array('r'=>'reservation'),array('count'=>'COUNT(creator)'))
               ->where('creator=?', (int) $userId, Zend_Db::INT_TYPE);
        return (int) $this->_db->fetchOne($select);    
    }
}
