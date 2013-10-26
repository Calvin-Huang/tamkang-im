<?php

namespace Admin\Event;

use Tool\Sql\SqlConnection;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Role\GenericRole as Role;
use Zend\Permissions\Acl\Resource\GenericResource as Resource;
use Zend\Session\Container;
use Tool\Security\AccessSecurity;

class Authentication
{
    private $acl = null;
    private $config = null;
    public function __construct()
    {
        $file = realpath(__DIR__ . "/../../../config/acl.config.php");
        if (!file_exists($file)) {
            throw new \Exception("Invalid ACL Config found");
        }
        $this->config = require realpath(__DIR__ . "/../../../config/acl.config.php");
        $this->acl = $this->initAcl($this->config);
    }
    
    public function addLoginLog($id)
    {
        $sqlConnection = new SqlConnection();
    
        $param[] = $id;
        $param[] = date("Y-m-d H:i:s");
        $query = "INSERT INTO `login_log` (`user_id`, `login_time`) VALUES (?, ?)";
    
        $sqlConnection->executeQuery($query, $param);
    }
    
    public function checkAuth($username, $password)
    {
        $sqlConnection = new SqlConnection();
        
        // 先取出role所有種類
        $query = "SELECT `id`, `role_name` FROM `user_role`";
        $role = $sqlConnection->executeQuery($query);
        
        $param[] = $username;
        $query = "SELECT U.`id`, R.`role_name`, R.`zh_TW`, U.`name`, U.`password`, U.`username`, U.`email`, U.`avatar` FROM `user` U ";
        $query.= "INNER JOIN `user_role` R ON R.`id` = U.`role_id` ";
        $query.= "WHERE BINARY U.`username` = ?";
        
        $result = $sqlConnection->executeQuery($query, $param, true);
        
        $password = md5($password);
        $password = sha1($password);

        if ($result != false && $result["password"] == $password) {
            $this->addLoginLog($result["id"]);

            $accessSecurity = new AccessSecurity();
            
            $authorized = new Container("authorized");
            $authorized->offsetSet("t", $accessSecurity->createAccessToken($result["id"], date("Y-m-d")));
            $authorized->offsetSet("name", $result["name"]);
            $authorized->offsetSet("username", $result["username"]);
            $authorized->offsetSet("role", $result["role_name"]);
            $authorized->offsetSet("role_zhTW", $result["zh_TW"]);
            $authorized->offsetSet("email", $result["email"]);
            $authorized->offsetSet("avatar", $result["avatar"]);
        }
    }
    
    public function clearIdentity()
    {
        $authorized = new Container("authorized");
        $authorized->offsetUnset("id");
        $authorized->offsetUnset("name");
        $authorized->offsetUnset("username");
        $authorized->offsetUnset("role");
        $authorized->offsetUnset("role_zhTW");
        $authorized->offsetUnset("email");
        $authorized->offsetUnset("avatar");
        $authorized->offsetUnset("t");
        $authorized->getManager()->getStorage()->clear("authorized");
    }
    
    public function getAcl()
    {
        return $this->acl;
    }
    
    public function getIdentity()
    {
        $identity = array();
        $authorized = new Container("authorized");
        $identity["t"] = $authorized->offsetGet("t");
        $identity["name"] = $authorized->offsetGet("name");
        $identity["username"] = $authorized->offsetGet("username");
        $identity["role"] = $authorized->offsetGet("role");
        $identity["role_zhTW"] = $authorized->offsetGet("role_zhTW");
        $identity["email"] = $authorized->offsetGet("email");
        $identity["avatar"] = $authorized->offsetGet("avatar");
        return $identity;
    }
    
    public function getConfig()
    {
        return $this->config;
    }
    
    public function hasIdentity()
    {
        
        $authorized = new Container("authorized");
        $lastTime = time();
        if ($authorized->offsetExists("time")) {
            $lastTime = $authorized->offsetGet("time");
        }
        $authorized->offsetSet("time", time());
        
        // 不存在認證id或時間閑置超過1小時的話，就返回沒有身份
        $overtime = (time() - $lastTime) > 3600;
        $authorized->offsetSet("overtime", $overtime);
        
        if ($overtime || !$authorized->offsetExists("t")) {
            
            // 除去認證
            $this->clearIdentity();
            return false;
        }
        
        return true;
    }
    
    public function initAcl($config)
    {
        $acl = new Acl();
        
        // add roles
        foreach ($config["acl"]["roles"] as $role => $parent) {
            $acl->addRole(new Role($role, $parent));
        }
        
        // add resources
        foreach ($config["acl"]["resources"] as $controller) {
            $acl->addResource(new Resource($controller));
        }
        
        // set up allow
        foreach ($config["acl"]["allow"] as $role => $controllers) {
            foreach ($controllers as $controller => $actions) {
                $acl->allow($role, $controller, $actions);
            }
        }
        
        return $acl;
    }
    
    public function setIdentity($identity)
    {
        $authorized = new Container("authorized");
        if (isset($identity["name"]) && $identity["name"] != "") {
            $authorized->offsetSet("name", $identity["name"]);
        }
        if (isset($identity["email"]) && $identity["email"] != "") {
            $authorized->offsetSet("email", $identity["email"]);
        }
        if (isset($identity["avatar"]) && $identity["avatar"] != "") {
            $authorized->offsetSet("avatar", $identity["avatar"]);
        }
    }
}
?>