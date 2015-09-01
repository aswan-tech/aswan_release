/*
* Validate admin login form
*/
function adminLogin(){
	var user_name = document.mylogin.user_name.value;
	var user_pass = document.mylogin.user_pass.value;
	if( user_name == ''){
		alert('Enter User Id !');
		return false;
	}
	else if( user_pass == ''){
		alert('Enter Password !');
		return false;
	}
}

function changePass(){
	var new_pass = document.changepassform.new_pass.value;
	var confirm_pass = document.changepassform.confirm_pass.value;
	if(new_pass == ''){
		alert('Enter New Password !');
		return false;
	}
	else if(confirm_pass == ''){
		alert('Enter Confirm Password !');
		return false;
	}
	else if(new_pass!= confirm_pass){
		alert('Confirm Password is not matched');
		return false;
	}
}
function addCategory(){
	var cat_name = document.catform.cat_name.value;
	if(cat_name == ''){
		alert('Enter Category Name !');
		return false;
	}
}
function addTopic(){
	var cat_name = document.addtopic.cat_name.value;
	var topic_name = document.addtopic.topic_name.value;
	var topic_title = document.addtopic.topic_title.value;
	var topic_des = document.addtopic.topic_des.value;
	if(cat_name == ''){
		alert('Select Category !');
		return false;
	}
	else if(topic_name == ''){
		alert('Enter Topic Name !');
		return false;
	}
	else if(topic_title == ''){
		alert('Enter Topic Title !');
		return false;
	}
	else if(topic_des == ''){
		alert('Enter Topic Description !');
		return false;
	}
}
function reviewStatusVal(){
	var review_status = document.review.review_status.value;
	if(review_status == '' ){
		alert('Please Select Review Status');
		return false;
	}
	else{
		return true;
	}
}

function addForum(){
	var forum_title = document.addforum.forum_title.value;
	var forum_des = document.addforum.forum_des.value;
	//var file = document.addforum.file.value;
	if(forum_title == ''){
		alert('Enter Forum Title !');
		return false;
	}
	else if(forum_des == ''){
		alert('Enter Forum Description !');
		return false;
	}
	// else if(file == ''){
		// alert('Upload Image!');
		// return false;
	// }
}