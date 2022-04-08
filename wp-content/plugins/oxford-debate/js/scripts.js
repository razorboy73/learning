// General scripts

jQuery(document).ready(function() {

   jQuery('#show-social-share').on('click',function(){
      jQuery('#social-share-hidden').toggle();
      jQuery('#show-social-share').toggleClass('active');
  
   });
    
    
    jQuery.ajax({

            url: jQuery("#vote-update-url").val(),
            type: "POST",
                data: {
                    debateId: jQuery("#debate-id").val(),
                },
                success: function (data) {
                    
                    jQuery("#response-container").html(data);
            }
	});
    
    
    var vote = "";

            jQuery("#vote-button-a").click(function(){
            vote = "a";
            jQuery.ajax({
            url: jQuery("#vote-process-url").val(),
            type: "POST",
            data: {
                aVotes: jQuery("#a-votes").val(),
                debateId: jQuery("#debate-id").val(),
                userId: jQuery("#user-id").val(),
                voteType: vote
            },
            success: function (data) {
                data = JSON.parse(data);
                jQuery("#response-container").html(data);
                jQuery("#vote-button-a").html('Voted');
                jQuery("#vote-button-a").attr('disabled', true);
                jQuery("#vote-button-b").attr('disabled', true);
                jQuery('html,body').animate({
                    scrollTop: jQuery("#debate-section").offset().top -100},
                    'slow');
                
            }
        });

    }); 
    
            jQuery("#vote-button-b").click(function(){
            vote = "b";
            jQuery.ajax({
            url: jQuery("#vote-process-url").val(),
            type: "POST",
            data: {
                aVotes: jQuery("#b-votes").val(),
                debateId: jQuery("#debate-id").val(),
                userId: jQuery("#user-id").val(),
                voteType: vote
            },
            success: function (data) {
                data = JSON.parse(data);
                jQuery("#response-container").html(data);
                jQuery("#vote-button-b").html('Voted');
                jQuery("#vote-button-b").attr('disabled', true);
                jQuery("#vote-button-a").attr('disabled', true);
                jQuery('html,body').animate({
                    scrollTop: jQuery("#debate-section").offset().top -100},
                    'slow');
                
            }
        });

    });
    

});


