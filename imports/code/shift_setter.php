<?PHP
require_once 'PHPExcel/IOFactory.php';
class Imports extends SFunctions
{
    private $basefile = '';	
    function __construct()
    {
        parent::__construct();
        $this->basefile = basename($_SERVER['PHP_SELF']);     
    }
    
    public function createForm()
    {
        echo '<div class="row">';
            echo '<div class="col-xs-6">';
                echo '<div id="shift_setter_gridID"></div>';
            echo '</div>';
            
            echo '<div class="col-xs-6">';
                echo '<div id="AllSetterGridsID"></div>';
            echo '</div>';
        echo '</div>';
    }
}
?>