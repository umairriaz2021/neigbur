jQuery(document).ready(function($){
    jQuery('.mobile_nav').click(function(){ openNav(); });
    jQuery('#send_email').on('click',function(e){
      e.preventDefault();
      var email = $('#email_id').val();
      var subject = $('#subject_name').val();
      var e_body = decodeURIComponent(tinyMCE.get('email_body').getContent());
      var action = 'mylibrary';
      var param = 'get_email_data';
      //console.log(myajaxurl);
      $.ajax({
          url:myajaxurl,
          type: 'POST',
          data:{email:email,subject:subject,action:action,param:param,e_body:e_body},
          dataType:'json',
          success:function(data){
              console.log(data);
              $('#close_btn').trigger('click');
              //alert('Message sent successfully');
              if(data['status'] == 200){
                  alert(data['message']);
                 $('#close_btn').trigger('click');
              }
              else if(data['status'] == 400){
                  alert(data['message']);
                 
              }
              
           
          }
        
      });
    });
    
    
});