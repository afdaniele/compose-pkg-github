<?php
use \system\classes\Core as Core;
use \system\classes\BlockRenderer as BlockRenderer;

class GitHubRepository extends BlockRenderer{

    static protected $ICON = [
        "class" => "fa",
        "name" => "github"
    ];

    static protected $ARGUMENTS = [];

    protected static function render( $id, &$args ){
        ?>
        <div style="padding:0 20px">
            <table style="width:100%; height:20%">
                <tr>
                    <td style="width:50%" class="text-left">

                    </td>
                    <td style="width:50%" class="text-right">
                        <?php
                        if( $args['private'] ){
                            ?>
                            Private &nbsp;<i class="fa fa-lock" aria-hidden="true" style="color:orange"></i>
                            <?php
                        }else{
                            ?>
                            Public &nbsp;<i class="fa fa-unlock-alt" aria-hidden="true" style="color:green"></i>
                            <?php
                        }
                        ?>
                    </td>
                </tr>
            </table>
        </div>

        <?php

        // print_r( $args );

        ?>



        <script type="text/javascript">
            $( document ).ready(function() {

            });
        </script>

        <?php
    }
}//GitHubRepository
?>
