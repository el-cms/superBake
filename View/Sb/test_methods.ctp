<h1>Method testings</h1>

<!--<h2>Choose default values</h2>
<strong>getModelsToGenerate($plugin<small>=null</small>, $complete<small>=false</small>)</strong> : getModelsToGenerate(<?php echo $this->Form->input('gmtg_plugin', array('label' => false, 'div' => false, 'class' => 'smallInput')) ?>,<?php echo $this->Form->input('gmtg_complete', array('label' => false, 'div' => false, 'type' => 'checkbox', 'value' => 'true', 'checked' => false, 'class' => 'smallInput')) ?>)<br />-->


<ul class="nav nav-tabs">
    <li class="active"><a href="#method_getModelsToGenerate" data-toggle="tab">getModelsToGenerate</a></li>
    <li><a href="#method_getControllersToGenerate" data-toggle="tab">getControllersToGenerate</a></li>
    <li><a href="#method_getViewsToGenerate" data-toggle="tab">getViewsToGenerate</a></li>
</ul>
<div class="tab-content">
    <div class="tab-pane active" id="method_getModelsToGenerate">
        <h2>getModelsToGenerate<small>($plugin, $complete=false)</small></h2>
        <div class="row">
            <div class="col-lg-6">
                <h3>GetModelsToGenerate('appBase')</h3>
                <pre>
                    <?php echo $this->Html->nestedList($gmtg_plug);?>
                </pre>
            </div>
            <div class="col-lg-6">
                <h3>GetModelsToGenerate('appBase', true)</h3>
                <pre>
                    <?php echo $this->Html->nestedList($gmtg_plug_complete);?>
                </pre>
            </div>
        </div>
    </div>
    <div class="tab-pane" id="method_getControllersToGenerate">
        <div class="row">
            <h2>getControllersToGenerate<small>($plugin=null)</small></h2>
        </div>
    </div>
    <div class="tab-pane" id="method_getViewsToGenerate">
        <div class="row">
            <h2>getViewsToGenerate<small>($plugin, $part=null)</small></h2>
        </div>
    </div>
</div>