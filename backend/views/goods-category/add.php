<?php
/**
 * Created by PhpStorm.
 * User: zhu-y
 * Date: 2017/9/10
 * Time: 11:41
 */
//表单开始
$form = \yii\bootstrap\ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]);
echo $form->field($model,'name')->textInput();
echo $form->field($model,'parent_id')->hiddenInput();

//-----------------ztree-----------------
echo '<ul id="treeDemo" class="ztree"></ul>';

//-----------------ztree-----------------
echo $form->field($model,'intro')->textarea();
echo '<button type="submit" class="btn btn-info">提交</button>';
//表单结束
\yii\bootstrap\ActiveForm::end();

/**
 * @var $this \yii\web\View
 */
//注册ztree的静态资源和js
//注册css文件
$this->registerCssFile('@web/ztree/css/zTreeStyle/zTreeStyle.css');
//注册js文件 (需要在jquery后面加载)
$this->registerJsFile('@web/ztree/js/jquery.ztree.core.js',['depends'=>\yii\web\JqueryAsset::className()]);

$goodsCategories = json_encode(\backend\models\GoodsCategory::getZNodes());
$this->registerJs(new \yii\web\JsExpression(
    <<<JS
    var zTreeObj;
   // zTree 的参数配置，深入使用请参考 API 文档（setting 配置详解）
   var setting = {
       data: {
           simpleData: {
               enable: true,
               idKey: "id",
               pIdKey: "parent_id",
               rootPId: 0
           }
       },
       callback: {
           onClick: function(event, treeId, treeNode) {
               console.log(treeNode);
               $("#goodscategory-parent_id").val(treeNode.id);
           }
       }
   };
   // zTree 的数据属性，深入使用请参考 API 文档（zTreeNode 节点数据详解）
   var zNodes = {$goodsCategories};
   
   zTreeObj = $.fn.zTree.init($("#treeDemo"), setting, zNodes);
        //展开全部节点
        zTreeObj.expandAll(true);
        //修改 根据当前分类的parent_id来选中节点
        //获取你需要选中的节点 
        var node = zTreeObj.getNodeByParam("id", "{$model->parent_id}", null);
        zTreeObj.selectNode(node);
 
JS

));