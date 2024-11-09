$(document).ready(function(){
            // HERO SLIDER
            $('#hero-slider').owlCarousel({
                loop:true,
                margin:10,
                nav:true, 
                items: 1,
                dots: false,// để làm mất 2 dấu chấm ở silde 
                smartSpeed: 1000,  // điều chỉnh lại tốc độ lướt theo mili giây
                responsive:{
                    0:{
                        
                    },
                    600:{
                        
                    },
                    1000:{
                   
                    }   
                }
            })
});


