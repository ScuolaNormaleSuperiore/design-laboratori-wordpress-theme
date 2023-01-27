<div id="pre-header" class="bg-petrol">
    <div class="container">
        <div class="row variable-gutters">
            <div class="col-6">
                <a href="https://www.sns.it/" target="_blank" aria-label="Scuola Normale Superiore - Collegamento esterno - Apre su nuova scheda">
                    <strong>Scuola Normale superiore</strong>
                </a>
            </div><!-- /col-6 -->
            <div class="col-6 header-utils-wrapper">
                <div class="header-utils">
                    <?php
                    if(!is_user_logged_in()) {
                        get_template_part("template-parts/header/header-anon");
                    }else{
                        get_template_part("template-parts/header/header-logged");
                    }
                    ?>
                </div><!-- /header-utils -->
            </div><!-- /col-6 -->
        </div><!-- /row -->
    </div><!-- /container -->
</div>
<?php
