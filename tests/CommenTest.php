<?php
include(__DIR__."/../vendor/autoload.php");
class CommenTest extends PHPUnit_Framework_TestCase
{
	public function testFormDataTextBox()
	{
		$this->assertEquals("", Lib\Common\Formdata::getTextbox("foo"));
		$this->assertEquals('value=""', Lib\Common\Formdata::getTextbox("foo", "", true));
		$this->assertEquals('value="max"', Lib\Common\Formdata::getTextbox("foo", "max", true));
		$this->assertEquals("max", Lib\Common\Formdata::getTextbox("foo", "max"));
		$_GET["foo"]="bar";
		$this->assertEquals("bar", Lib\Common\Formdata::getTextbox("foo"));
		$this->assertEquals("bar", Lib\Common\Formdata::getTextbox("foo", "max"));
		$this->assertEquals('value="bar"', Lib\Common\Formdata::getTextbox("foo", "", true));
	}
	public function testFormDataCheckBox()
	{
		$this->assertEquals("", Lib\Common\Formdata::getCheckbox("foo"));
		$this->assertEquals(False, Lib\Common\Formdata::getCheckbox("foo"));
		$this->assertEquals(True, Lib\Common\Formdata::getCheckbox("foo", True));
		$this->assertEquals(False, Lib\Common\Formdata::getCheckbox("foo", False));
		$this->assertEquals('', Lib\Common\Formdata::getCheckbox("foo", False, True));
		$_GET["foo"]="bar";
		$this->assertEquals("", Lib\Common\Formdata::getCheckbox("foo"));
		$this->assertEquals(False, Lib\Common\Formdata::getCheckbox("foo"));
		$this->assertEquals(True, Lib\Common\Formdata::getCheckbox("foo", True));
		$this->assertEquals(False, Lib\Common\Formdata::getCheckbox("foo", False));
		$_GET["foo"]="on";
		$this->assertEquals(True, Lib\Common\Formdata::getCheckbox("foo"));
		$this->assertEquals(True, Lib\Common\Formdata::getCheckbox("foo", False));
		$this->assertEquals('checked="checked"', Lib\Common\Formdata::getCheckbox("foo", False, True));
		$_GET["foo"]="off";
		$this->assertEquals(False, Lib\Common\Formdata::getCheckbox("foo"));
		$this->assertEquals(False, Lib\Common\Formdata::getCheckbox("foo", True));
		$this->assertEquals('', Lib\Common\Formdata::getCheckbox("foo", False, True));
		$this->assertEquals('', Lib\Common\Formdata::getCheckbox("bar", False, True, "foo"));
		$this->assertEquals('', Lib\Common\Formdata::getCheckbox("bar", True, True, "foo"));
		$_GET["bar"]="on";
		$this->assertEquals('checked="checked"', Lib\Common\Formdata::getCheckbox("bar", False, True, "foo"));
		$this->assertEquals(True, Lib\Common\Formdata::getCheckbox("bar", True, False, "foo"));

	}

	public function testFormDataSelectBox()
	{
		$this->assertEquals('', Lib\Common\Formdata::getSelect("foo", "5"));
		$this->assertEquals(False, Lib\Common\Formdata::getSelect("foo", "5"));
		$this->assertEquals('selected="selected"', Lib\Common\Formdata::getSelect("foo", "5", 5));
		$this->assertEquals(True, Lib\Common\Formdata::getSelect("foo", "5", 5, False));
		$_GET["foo"]=5;
		$this->assertEquals('selected="selected"', Lib\Common\Formdata::getSelect("foo", "5"));
		$this->assertEquals(False, Lib\Common\Formdata::getSelect("foo", "6"));
		$this->assertEquals('', Lib\Common\Formdata::getSelect("foo", "6", 6));
		$this->assertEquals(False, Lib\Common\Formdata::getSelect("foo", "6", 6, False));
		$this->assertEquals(True, Lib\Common\Formdata::getSelect("foo", "5", NULL, False));
	}

	public function testBahnIsStation()
	{
		$stations = array(array("value"=>"Berlin HBF"), array("value"=>"Hannover Hbf"), array("value"=>"Hannover Leinhausen"));
		$this->assertEquals(False, Lib\Common\Bahn::isBahnhof("", $stations));
		$this->assertEquals(False, Lib\Common\Bahn::isBahnhof("Berlin Hbf", $stations));
		$this->assertEquals(False, Lib\Common\Bahn::isBahnhof("Hannover", $stations));
		$this->assertEquals(True, Lib\Common\Bahn::isBahnhof("Hannover Hbf", $stations));
		$this->assertEquals(True, Lib\Common\Bahn::isBahnhof("Berlin HBF", $stations));
	}
	public function testBahnFilter()
	{
		$t = time();
		date_default_timezone_set("Europe/Berlin");
		$train = array("abfahrt" => date("H:i"), "station" => array(array("timestamp" => $t+10, "name"=>"Station 1")));
		$trains[] = $train;
		$res = Lib\Common\Bahn::filter($trains, 0, 20);
		$this->assertEquals(1, count(Lib\Common\Bahn::filter($trains, $t, $t+20)));
		$this->assertEquals(0, count(Lib\Common\Bahn::filter($trains, $t+0, $t+5)));
		$train = array("abfahrt" => date("H:i", $t), "station" => array(array("timestamp" => $t+15, "name"=>"Station 1"),array("timestamp" => $t+240, "name"=>"Station 2")));
		$trains[] = $train;
		$this->assertEquals(3, count(Lib\Common\Bahn::filter($trains, $t+0, $t+500)));
		$this->assertEquals(2, count(Lib\Common\Bahn::filter($trains, $t+0, $t+25)));
		$this->assertEquals(1, count(Lib\Common\Bahn::filter($trains, $t+14, $t+20)));
		$this->assertEquals(0, count(Lib\Common\Bahn::filter($trains, $t+0, $t+20, 1)));

		$this->assertEquals(1, count(Lib\Common\Bahn::filter($trains, $t+0, $t+500, 1)));
	}
}
