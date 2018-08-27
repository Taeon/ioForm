<?php

error_reporting(-1);
ini_set('display_errors', 1);
ini_set('default_charset', 'UTF-8');

// Avoid strict errors when timezone not set
date_default_timezone_set( 'Europe/London' );

try{
$start_time = microtime(true);

	require_once( 'autoloader.php' );
	require_once( '../../iovalidate/web/autoloader.php' );
	require_once( 'foo/Indenter.php' );

	//$form = (new \ioForm\Form())->FromArray(
	//	array(
	//		'action' => '/process-form',
	//		'id' => 'testform',
	//		'elements' => array(
	//			array(
	//				'type' => 'Layout:Fieldset',
	//				'elements' => array(
	//					array(
	//						'type' => 'Text',
	//						'name' => 'text1',
	//						'alias' => 'Foo',
	//						'label' => 'A text element',
	//						'default' => '1'
	//					),
	//					array(
	//						'type' => 'Radio',
	//						'name' => 'radio',
	//						'label' => 'A radio element',
	//						'default' => '1',
	//						'options' => array(
	//							array( 'value' => 1, 'text' => '1' ),
	//							array( 'value' => 2, 'text' => '2' ),
	//						)
	//					)
	//				)
	//			),
	//		),
	//		'buttons' => array(
	//			array( 'type' => 'submit', 'value' => 'Submit this form!' )
	//		)
	//	)
	//);
	//$foo = ( new \ioForm\Core\Definition() )->FromArray
	//(
	//	array(
	//		'type' => 'Layout:Fieldset',
	//		'elements' => array(
	//			array(
	//				'type' => 'Text',
	//				'name' => 'text2',
	//				'alias' => 'Foo2',
	//				'label' => 'A text element',
	//				'default' => '1'
	//			),
	//			array(
	//				'type' => 'Radio',
	//				'name' => 'radio2',
	//				'label' => 'A radio element',
	//				'default' => '1',
	//				'options' => array(
	//					array( 'value' => 1, 'text' => '1' ),
	//					array( 'value' => 2, 'text' => '2' ),
	//				)
	//			)
	//		)
	//	)
	//);
	//$form->GetElementByAlias( 'Foo' )->After( $foo );

$pattern = '^[a-z0-9_-]*$';
$form = (new \ioForm\Form())
->FromArray(
    array(
        'action' => '/newsletter-form.php',
        'elements' => array(
			array(
				'label' => 'Your name:',
				'type' => 'text',
				'default' => 'Foo',
				'placeholder' => 'Enter your name',
				'name' => 'your_name'
			),
            array(
				'label' => 'Your email address:',
				'type' => 'date',
				'placeholder' => 'Enter your email address',
				'name' => 'your_date'
			)
        )
    )
);
$form->SetValue( 'your_name', 'asd' );

print_r( $form->GetValue( 'your_name' ) );

// $validator = ( new \ioValidate\Definition() )->FromForm(  $form  );
// $values =  (new \ioValidate\Values())->FromArray( array(
// 	'your_name' => 'foo@bar.com',
// 	'foo' => 'foo@bar.com'
// ));
// if( !$validator->Validate( $values ) ){
// 	echo('Invalid<br/>');
// } else {
// 	echo('Valid<br/>');
// }
// echo('<br/>');
// $validator = ( new \ioValidate\Definition() )->FromForm(  $form  );
// $values =  (new \ioValidate\Values())->FromArray( array(
// 	'your_name' => 'foo',
// 	'foo' => 'foo@bar.com'
// ));
// if( !$validator->Validate( $values ) ){
// 	echo('Invalid<br/>');
// } else {
// 	echo('Valid<br/>');
// }
// echo('<br/>');

} catch( Exception $e ){
	echo("<PRE>");
	print_r( $e );
}
?>
<html>
    <head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <script src="src/js/utils.js"></script>
        <script src="dist/js/ioform.js"></script>
        <script src="iovalidate/dist/js/iovalidate.js"></script>
		<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"/>
        <script type="text/javascript">

        </script>


    </head>
    <body>
<?=$form->Render()?>

<?=microtime( true ) - $start_time?>

<pre>
<?php
$indenter = new \Gajus\Dindent\Indenter();
echo( htmlentities( $indenter->indent($form->Render()) ) );

?>
</pre>

        <script type="text/javascript">

function ready(fn) {
  if (document.readyState != 'loading'){
    fn();
  } else {
    document.addEventListener('DOMContentLoaded', fn);
  }
}
ready(
function(){
//document.getElementById( 'testform' ).onsubmit = function(){alert(1);};
var testform = new ioForm( '#testform' );
//testform.GetField('my_range_value').on('change',function(){console.log(this.value)})
var validator = new ioValidate( testform );
testform.on(
	'submit',
	function( event ){
		validator.DoFormValidation( event );
	}
)
    //testform.on( 'ioform:ready', function(){console.log('ready');} );
//testform.GetField( 'my_text' ).SetValue( '12345' );
//console.log( testform.GetField( 'my_text' ).GetElement().validity.patternMismatch );

    //testform.GetField( 'hidden1' ).SetValue('Hidden1');
    //console.log( testform.GetField( 'hidden1' ).GetValue() );
    //
    //testform.GetField( 'text1' ).SetValue('Text1');
    //console.log( testform.GetField( 'text1' ).GetValue() );
    //
    //testform.GetField( 'password1' ).SetValue('Password1');
    //console.log( testform.GetField( 'password1' ).GetValue() );
    //
    //testform.GetField( 'textarea1' ).SetValue('Textarea1');
    //console.log( testform.GetField( 'textarea1' ).GetValue() );
    //
    //testform.GetField( 'select1' ).SetValue('2');
    //console.log( testform.GetField( 'select1' ).GetValue() );
    //
    //testform.GetField( 'select2' ).SetValue(['1','3']);
    //console.log( testform.GetField( 'select2' ).GetValue() );
    //
    //testform.GetField( 'radio1' ).SetValue(2);
    //console.log( testform.GetField( 'radio1' ).GetValue() );
    //
    //testform.GetField( 'checkbox1' ).SetValue( true );
    //console.log( testform.GetField( 'checkbox1' ).GetValue() );
    //
    //testform.GetField( 'date1' ).SetValue( '2016-02-01' );
    //console.log( testform.GetField( 'date1' ).GetValue() );

    //testform.GetField( 'number1' ).SetValue( '-1.2' );
    //console.log( testform.GetField( 'number1' ).GetValue() );

}
);
        </script>

    </body>
</html>
