<?php


$story_text_eng = "What is Lorem Ipsum?
Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.

Why do we use it?
It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using 'Content here, content here', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for 'lorem ipsum' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).

Where does it come from? 
Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of \"de Finibus Bonorum et Malorum\" (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory of ethics, very popular during the Renaissance. The first line of Lorem Ipsum, \"Lorem ipsum dolor sit amet..\", comes from a line in section 1.10.32.

The standard chunk of Lorem Ipsum used since the 1500s is reproduced below for those interested. Sections 1.10.32 and 1.10.33 from \"de Finibus Bonorum et Malorum\" by Cicero are also reproduced in their exact original form, accompanied by English versions from the 1914 translation by H. Rackham.


Where can I get some?
There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don\'t look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn't anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet. It uses a dictionary of over 200 Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks reasonable. The generated Lorem Ipsum is therefore always free from repetition, injected humour, or non-characteristic words etc.";

/*$story_text_hindi = "पढने हुआआदी तकनिकल बिन्दुओमे सहयोग आधुनिक अंतर्गत यन्त्रालय पहोच। हैं। वर्णित प्रोत्साहित कलइस विश्व ब्रौशर विनिमय सकती चुनने पहेला गुजरना आशाआपस बिन्दुओ एकएस विश्व सभिसमज सम्पर्क उपलब्धता भारत जानते करती उद्योग असरकारक तकरीबन हुएआदि बढाता लिये पुर्व सारांश प्रमान अनुवाद जिवन संसाध लेने स्थिति प्राथमिक गटकउसि अनुवादक हार्डवेर सकती बनाने

संसाध बदले यायेका वैश्विक शारिरिक उनका परस्पर स्वतंत्रता सीमित बिन्दुओ कार्यकर्ता प्रतिबध्दता पहोच बाटते करने मुक्त वास्तव आंतरकार्यक्षमता परिवहन जागरुक विश्लेषण खरिदे जोवे अधिकार कार्यकर्ता पुर्व नीचे सुना कराना प्रमान सामूहिक विकेन्द्रियकरण जाने खरिदने निर्माता ढांचा मुख्य वर्ष उपलब्ध है।अभी सभीकुछ विवरन खरिदने अनुकूल प्रोत्साहित पेदा

द्वारा अधिकांश प्राथमिक सुनत सहयोग समजते निर्देश पुर्णता प्रव्रुति सकता देने बाजार आशाआपस निरपेक्ष विकेन्द्रित विशेष थातक स्वतंत्र प्राथमिक ज्यादा प्राथमिक पुष्टिकर्ता औषधिक कारन बलवान भाषा असक्षम व्यवहार केन्द्रित बढाता सारांश केन्द्रिय अर्थपुर्ण एछित आजपर खयालात सम्पर्क प्राधिकरन नवंबर देखने लिये विस्तरणक्षमता आंतरकार्यक्षमता अन्तरराष्ट्रीयकरन

बलवान मुश्किल वार्तालाप सेऔर मुख्य बिन्दुओमे मार्गदर्शन गटको क्षमता माध्यम उपलब्धता देते जानते हैं। सादगि सारांश दोषसके कार्यकर्ता किके लचकनहि क्षमता। बढाता खयालात उपलब्धता मर्यादित भीयह विवरन डाले। बिना व्यवहार पहोचाना विचरविमर्श पुर्णता विकेन्द्रित ";

*/
$story_categories = array("pos"=>"Positive","fic" => "Fiction", "ppl" => "People");

if(isset($_REQUEST["id"])){
	$story_id = $_REQUEST["id"];

	switch($story_id){
		case "1234":
		$story_obj = array(id => $story_id, view_count => 194734, title => "The story of Lorem Ipsum", author => "Arpit Chaudhary", categories => array($story_categories["fic"],$story_categories["pos"]), image_url => "http://www.la1380.com/wp-content/uploads/2014/10/beach.jpg", text=>array("English" => $story_text_eng));
		break;

		case "1235":
		$story_obj = array(id => $story_id, view_count => 22463, title => "Scaling new heights", author => "Anonymous", categories => array($story_categories["ppl"],$story_categories["pos"]), image_url => "http://upload.wikimedia.org/wikipedia/commons/f/f0/Everest_North_Face_toward_Base_Camp_Tibet_Luca_Galuzzi_2006_edit_1.jpg", text=>array("English" => $story_text_eng));
		break;

		case "1236":
		$story_obj = array(id => $story_id, view_count => 55093, title => "Finding my Nemo", author => "A Fisherman", categories => array($story_categories["fic"]), image_url => "http://resources0.news.com.au/images/2011/12/12/1226219/510332-cm-nemo-650.jpg", text=>array("English" => $story_text_eng));
		break;

		case "1237":
		$story_obj = array(id => $story_id, view_count => 20937, title => "Sandy Times", author => "Rain Man", categories => array($story_categories["ppl"]), image_url => "http://s3.amazonaws.com/kidzworld_photo/images/2013519/6b4640d8-fea0-4e63-b3a3-461ceaa1468d/sahara-gallery.jpg", text=>array("English" => $story_text_eng));
		break;

		case "1238":
		$story_obj = array(id => $story_id, view_count => 76234, title => "The Blue You", author => "Himanshu Luthra", categories => array($story_categories["fic"], $story_categories["ppl"]), image_url => "http://www.anubis-travel.com/wp-content/uploads/2015/02/Santorini-Greece-low.jpg", text=>array("English" => $story_text_eng));
		break;

		case "1239":
		$story_obj = array(id => $story_id, view_count => 87553, title => "A box of chocolates?", author => "Fat Man", categories => array($story_categories["pos"]), image_url => "https://s-media-cache-ak0.pinimg.com/originals/c9/f7/a3/c9f7a3b37b0d787728c5dc30761206d5.jpg", text=>array("English" => $story_text_eng));
		break;

		case "1240":
		$story_obj = array(id => $story_id, view_count => 239553, title => "Through a black lense", author => "Sharad Baliyan", categories => array($story_categories["pos"]), image_url => "https://fbcdn-sphotos-d-a.akamaihd.net/hphotos-ak-xfa1/v/t1.0-9/545128_10150995335190469_366773047_n.jpg?oh=09ac2f97f36be4de872e2c176871a421&oe=557F6D6B&__gda__=1433771356_e29e225f34eb43ec913a818137add319", text=>array("English" => $story_text_eng));
		break;

		default:
		$story_obj = array("error" => "Story id invalid");
		break;
	}

	
}else{
	$story_obj = array("error" => "Story id parameter not specified");
}

echo json_encode($story_obj);
//print_r($story_obj);
?>
