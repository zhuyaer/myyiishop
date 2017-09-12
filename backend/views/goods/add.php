<?php
/**
 * Created by PhpStorm.
 * User: zhu-y
 * Date: 2017/9/11
 * Time: 14:12
 */
use yii\web\JsExpression;
//表单开始
$form = \yii\bootstrap\ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]);
//echo $form->field($goods_intro,'content')->widget('kucha\ueditor\UEditor',[]);
echo $form->field($goods,'name')->textInput();
//echo $form->field($goods,'sn')->textInput();

//输出img标签
echo \yii\bootstrap\Html::img($goods->logo,['id'=>'img']);
echo $form->field($goods, 'logo')->hiddenInput();
//============uploadifive开始
//外部TAG
echo \yii\bootstrap\Html::fileInput('test', NULL, ['id' => 'test']);
echo \flyok666\uploadifive\Uploadifive::widget([
    'url' => yii\helpers\Url::to(['s-upload']),
    'id' => 'test',
    'csrf' => true,
    'renderTag' => false,
    'jsOptions' => [
        'formData'=>['someKey' => 'someValue'],
        'width' => 120,
        'height' => 40,
        'onError' => new JsExpression(<<<EOF
function(file, errorCode, errorMsg, errorString) {
    console.log('The file ' + file.name + ' could not be uploaded: ' + errorString + errorCode + errorMsg);
}
EOF
        ),
        'onUploadComplete' => new JsExpression(<<<EOF
function(file, data, response) {
    data = JSON.parse(data);
    if (data.error) {
        console.log(data.msg);
    } else {
        console.log(data.fileUrl);
        //将上传文件的路径写入logo字段的隐藏域
        $("#goods-logo").val(data.fileUrl);
        //图片回县
        $('#img').attr('src',data.fileUrl);    
            }
}
EOF
        ),
    ]
]);
//============uploadifive结束

//zTree.树状.商品分类显示
echo $form->field($goods,'goods_category_id')->hiddenInput();
echo '<div><ul id="treeDemo" class="ztree"></ul></div>';
//品牌分类下拉表
echo $form->field($goods,'brand_id')->dropDownList(\yii\helpers\ArrayHelper::map($brand,'id','name'),['prompt' => '==请选择品牌==']);
//市场价格,商品价格
echo $form->field($goods,'market_price')->input('number');
echo $form->field($goods,'shop_price')->input('number');
echo $form->field($goods,'stock')->input('number');
//是否在售,状态
echo $form->field($goods,'is_on_sale',['inline'=>true])->radioList([0 =>'下架',1=>'在售'],['class'=>'label-group']);
echo $form->field($goods,'status',['inline'=>true])->radioList([0 =>'回收站',1=>'正常'],['class'=>'label-group']);
//排序
echo $form->field($goods,'sort')->input('number');
//编辑器.
echo $form->field($goods_intro,'content')->widget('kucha\ueditor\UEditor',[]);
//提交
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
//表单结束
\yii\bootstrap\ActiveForm::end();

/**
 * @var $this \yii\web\View
 *
 */
//注册zTree的JS与CSS.
$this->registerCssFile('@web/ztree/css/zTreeStyle/zTreeStyle.css');
$this->registerJsFile('@web/ztree/js/jquery.ztree.core.js',['depends'=>\yii\web\JqueryAsset::className()]);
$goodsCategories = json_encode(\backend\models\GoodsCategory::getZNodes());
$this->registerJs(new \yii\web\JsExpression(
    <<<JS
        var zTreeObj;
       //zTree 的参数配置，深入使用请参考 API 文档（setting 配置详解）
        var setting = {
           data: {
                simpleData: {
                    enable: true,
                    idKey: "id",
                    pIdKey: "parent_id",
                    rootPId: 0
		        }
		   },     
           callback: {//事件回调函数
                    onClick: function( event, treeId, treeNode) {
                     console.log(treeNode);
		           //获取当前点击节点的id,写入parent_id的值
		             $("#goods-goods_category_id").val(treeNode.id);
                    }
           },
           view: {
		         showIcon: false
	        }
       };
       // zTree 的数据属性，深入使用请参考 API 文档（zTreeNode 节点数据详解）
       var zNodes = {$goodsCategories};
       zTreeObj = $.fn.zTree.init($("#treeDemo"), setting, zNodes);
        //展开全部节点
       zTreeObj.expandAll(true);
        //修改 根据当前分类的parent_id来选中节点
        //获取你需要选中的节点 
        var node = zTreeObj.getNodeByParam("id", "{$goods->goods_category_id}", null);
        zTreeObj.selectNode(node);
JS
));