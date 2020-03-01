$(function () {
    
    function querySubmit() {
        $('.searchBtn').click(() => {
            $('#queryForm').submit();
        });
    }
    querySubmit();

    function showProfil() 
    {
        if ($(window).width() > 992) 
        {
            $('.profil_icon').mouseenter(() => 
            {
                $('.profil').css({
                    transform: 'translateY(0)',
                    opacity: 1,
                    pointerEvents: 'all'
                });
            });
    
            $('.profil_icon').mouseleave(() => 
            {
                $('.profil').css({
                    transform: '',
                    opacity: '',
                    pointerEvents: ''
                });
            });
        }

        if ($(window).width() < 992) 
        {
            $('#open__profile__user__js').click(() => 
            {
                $('.profil').css({
                    transform: 'translateY(0)',
                    opacity: 1,
                    pointerEvents: 'all'
                });
            });
        }
    }
    showProfil();

    function burgerLine() {
        $('.burgerLine').mouseenter(() => {
            $('.burgerLine').toggleClass('toggleBurger');
        });
        $('.burgerLine').mouseleave(() => {
            $('.burgerLine').toggleClass('toggleBurger');
        });
    }
    burgerLine();

    /************** Effet d'hover sur Qui sommes-nous et Contact ? **************/
    $('.whoWeAre a').mouseover(() => {
        $('.whoWeAre i').css('color', '#fff');
    });
    $('.whoWeAre a').mouseout(() => {
        $('.whoWeAre i').css('color', '');
    });

    $('.contact a').mouseover(() => {
        $('.contact i').css('color', '#fff');
    });
    $('.contact a').mouseout(() => {
        $('.contact i').css('color', '');
    });

    /************** Fin d'effet d'hover sur submenu ? **************/
    $('#navigation li').each((key, value) => {
        $(value).mouseover(() => {
            link = $(value).children();
            $(link).not(this).css('color', '');
            $(link).css('color', '#fff');
        });
        $(value).mouseout(() => {
            $(link).css('color', '');
        });
    });

    /************** Fin d'effet d'hover sur Qui sommes-nous et Contact ? **************/

    function showRecherche() 
    {
        if ($(window).width() < 992) 
        {
            $('.close__form__mobile').click(() => 
            {
                $('.query__block').fadeOut();
            });

            $('#search').on('keyup', () => 
            {
                $('#viewSearch').fadeIn();

                //let saisie = $('#search').val();
                let url = $('#queryForm').attr('action');
                let data = $('#queryForm').serialize();

                $.ajax({
                    method: 'post',
                    url: url,
                    data: data,
                    dataType: 'json',
                    success: (response) => {
                        if (response.success) {
                            $('#viewSearch').html(response.results);
                        }
                        if (response.errors) {
                            $('#viewSearch').html('<div class="alert alert-danger">' + response.errors + '</div>');
                        }

                    }
                });

            });
        }

        if ($(window).width() > 992) 
        {
            $('#search').on('keyup', () => 
            {
                $('#viewSearch').fadeIn();
                $('.query').css({
                    borderRadius: '3px 3px 0 0'
                });
    
                //let saisie = $('#search').val();
                let url = $('#queryForm').attr('action');
                let data = $('#queryForm').serialize();
    
                $.ajax({
                    method: 'post',
                    url: url,
                    data: data,
                    dataType: 'json',
                    success: (response) => {
                        if(response.success) 
                        {
                            $('#viewSearch').html(response.results);
                        }
                        if(response.errors) 
                        {
                            $('#viewSearch').html('<div class="alert alert-danger">'+ response.errors +'</div>');
                        }
                        
                    }
                });
                
            });
        }

        $('#search').on('blur', () => {
            $('#viewSearch').fadeOut();
            $('.query').css({
                borderRadius: ''
            });
        });
    }
    showRecherche();

    function connectFormSubmit() {
        let usernameRegex = /^[\w.-]{3,20}$/;

        $('#connectForm').on('submit', () => {
            $('.alert-danger').remove();

            if ($('#username').val().trim() == '') {
                $('#connectForm').before('<div class="alert alert-danger">Veuillez saisir un nom d\'utilisateur.</div>')
                return false;
            }
            else if (!usernameRegex.test($('#username').val())) {
                $('#connectForm').before('<div class="alert alert-danger">Votre nom d\'utilisateur doit comporter entre 3 et 20                 caractères (a à z, A à Z, 0 à 9 et .,-,_).</div>')
                return false;
            }

            if ($('#password').val().trim() == '') {
                $('#connectForm').before('<div class="alert alert-danger">Veuillez entrer un mot de passe valide.</div>')
                return false;
            }

        });
    }
    connectFormSubmit();

    function confirmDelete() 
    {
        $('.confirm').click(() => {
            return confirm('Êtes-vous certain de vouloir supprimer ce produit ?');
        });
        $('.confirmLivraisonDel').click(() => {
            return confirm('Êtes-vous certain de vouloir supprimer cette adresse ?');
        });
        $('.confirmCommandeDel').click(() => {
            return confirm('Êtes-vous certain de vouloir supprimer cette commande ?');
        });
    }
    confirmDelete();

    function addCollectionWidget() {
        $('.add-another-collection-widget').click((e) => {
            let eTarget = $('.add-another-collection-widget');
            let list = $($(eTarget).attr('data-list-selector'));
            // Try to find the counter of the list or use the length of the list
            let counter = list.data('widget-counter') | list.children().length;

            // grab the prototype template
            let newWidget = list.attr('data-prototype');
            // replace the "__name__" used in the id and name of the prototype
            // with a number that's unique to your emails
            // end name attribute looks like name="contact[emails][2]"
            newWidget = newWidget.replace(/__name__/g, counter);
            // Increase the counter
            counter++;
            // And store it, the length cannot be used if deleting widgets is allowed
            list.data('widget-counter', counter);

            // create a new list element and add it to the list
            let newElem = $(list.attr('data-widget-tags')).html(newWidget);
            newElem.appendTo(list);
        });
    }
    addCollectionWidget();

    function viewProduitImages() 
    {
        $('#produits-image-fields-list').after('<div id="view" class="mb-4"></div>');

        $('#produits_image_0_imageFile_file').on('change', (e) => 
        {
            alert('Bolok est mortel')
            let reader = new FileReader();
            reader.onload = (e) => {
                $('#view').html('<img src="' + e.target.result + '" alt="Toto est mortel" class="img-fluid">');
            }
            reader.readAsDataURL(e.target.files[0]);
        });

    }
    viewProduitImages();

    function viewOtherPhotos() {
        let large = $('#large');
        let thumbnail = $('.thumbnailImg');

        thumbnail.each((key, value) => {
            $(value).click((e) => {
                e.preventDefault();
                let src = $(value).attr('src');
                //console.log(src);
                $(large).attr('src', src);
                $(large).parent().attr('href', src);
            });
        });
    }
    viewOtherPhotos();

    function ficheProduit() 
    {
        let zoomConfig = 
        {
            cursor: 'crosshair', 
            zoomType: 'inner',
            zoomWindowFadeIn: 500,
            zoomWindowFadeOut: 750 
        };
        let image = $('.thumbImg');
        let zoomImage = $('#largeZoom');

        let thumbnailBlock = $('.thumbnail');

        thumbnailBlock.each((key, element) => {
            $(element).click(() => {
                $(thumbnailBlock).not($(element)).removeClass('ficheActive');
                $(element).addClass('ficheActive');
            });
        });

        zoomImage.elevateZoom(zoomConfig);//initialise zoom

        image.each((key, element) => 
        {
            $(element).on('click', () => 
            {
                // Remove old instance od EZ
                $('.zoomContainer').remove();
                zoomImage.removeData('elevateZoom');
                // Update source for images
                zoomImage.attr('src', $(element).data('image')); // Update data-image attribute
                zoomImage.data('zoom-image', $(element).data('zoom-image'));// Update data-zoom-image attribute
                // Reinitialize EZ
                zoomImage.elevateZoom(zoomConfig);

                let src = $(element).attr('src');
                //console.log(src);
                zoomImage.attr('src', src);
                zoomImage.parent().attr('href', src);
            });
        });

    }
    ficheProduit();

    function srollBtn() {
        $('.scroller').mouseenter(() => {
            $('.textScroll').fadeIn().animate({
                top: '-40px',
                opacity: 1,
                pointerEvents: 'all'
            }, 250);
        });
        $('.scroller').mouseleave(() => {
            $('.textScroll').animate({
                top: '-10px',
                opacity: 0,
                pointerEvents: 'none'
            }, 125);
        });

        $('.scroller').click(() => {
            let cible = $('html, body');
            $(cible).animate({
                scrollTop: $(cible).offset().top
            }, 1000);

            $('.textScroll').fadeOut();
        });

        $(window).scroll(() => {
            if ($(this).scrollTop() > 40) {
                $('.scrollBtn').fadeIn('slow');
            } else {
                $('.scrollBtn').fadeOut('slow');
            }
        });

    }
    srollBtn();

    function priceSlide() 
    {
        $('#min_price').on('input', () => {

            let price = $('#min_price').val();
            $('#price_range').text('Produits à plus de ' + price + ' €');
            //$('#priceForm').submit();
            let url = $('#priceForm').attr('action');
            let data = $('#priceForm').serialize();
            //alert(data);

            $.ajax({
                method : 'post',
                url : url,
                data : data,
                cache : false,
                dataType : 'json',
                success : (response) => {
                    //console.log(response); 
                    if(response.success) 
                    {
                        $('.jsProductsDisplay').fadeIn().html(response.results);
                    }   
                    if(response.errors) 
                    {
                        $('.jsProductsDisplay').fadeIn().html('<div class="alert alert-danger">'+ response.errors +'</div>');
                    }  

                }

            });
            //window.history.replaceState({}, '', url);

        });
    }
    priceSlide();

    function breakGradient() {
        $('.breakGradient').click(() => {
            $('.breakGradient').hide();
            $('.desc').animate({
                minHeight: '350px'
            }, 1000);
            $('#gradient').fadeOut(1000);
        });
    }
    //breakGradient();

    function showFilters() {
        let toggle = false;

        $('.trieBy').click(() => {

            if ($('.sizeColorFilter:visible').length != 0) {
                $('.sizeColorFilter').slideUp();
                $('.priceFilter').slideDown('slow');
                $('.trieBy').css({
                    borderRadius: '3px 3px 0 0'
                });
                $('.filtres').css({
                    borderRadius: '3px'
                });
            } else {
                $('.priceFilter').slideToggle('slow');
            }

            if (toggle === false) {
                $('.trieBy').css({
                    borderRadius: '3px 3px 0 0'
                });
                toggle = true;
            } else {
                $('.trieBy').css({
                    borderRadius: ''
                });
                toggle = false;
            }
        });

        $('.filtres').click(() => {
            if ($('.priceFilter:visible').length != 0) {
                $('.priceFilter').slideUp('slow');
                $('.sizeColorFilter').slideDown();
                $('.filtres').css({
                    borderRadius: '3px 3px 0 0'
                });
                $('.trieBy').css({
                    borderRadius: '3px'
                });
            } else {
                $('.sizeColorFilter').slideToggle('slow');
            }

            if (toggle === false) {
                $('.filtres').css({
                    borderRadius: '3px 3px 0 0'
                });
                toggle = true;
            } else {
                $('.filtres').css({
                    borderRadius: ''
                });
                toggle = false;
            }
        });
    }
    showFilters();

    /*function flipFilter() {
        let cards = $('.js_card');
        cards.each((key, element) => 
        {
            $(element).click(() => 
            {
                
                
            });
        });
    }
    flipFilter();*/

    function productsJsFilters() 
    {
        let categories = $('.js-filterCat a');
        let tailles = $('.js-filterTai a');
        let couleurs = $('.js-filterColors a');
        let minPrice = $('.js-filterMinPrice');
        let maxPrice = $('.js-filterMaxPrice');

        function showLoader() 
        {
            $('.jsProductsDisplay').addClass('isloading');
            let spin = $('.spinLoader').css('opacity', 1);
            function fade() {
                spin.css('opacity', 0);
            }
            setTimeout(fade, 1000);
        }
        function closeLoader() 
        {
            $('.jsProductsDisplay').removeClass('isloading');
        }

        categories.each((key, element) => 
        {
            $(element).click((e) => 
            {
                e.preventDefault();
                let url = $(element).attr('href');
                let data = $(element).text().toLowerCase();
                
                $.ajax(
                {
                    method      : 'post',
                    url         : url,
                    data        : data,
                    dataType    : 'json',
                    beforeSend : () => 
                    {
                        showLoader();
                    },
                    success     : (response) => 
                    {
                        if(response.success) 
                        {
                            closeLoader();
                            $('.jsProductsDisplay').fadeIn().html(response.results);
                        }
                        if (response.errors) {
                            $('.jsProductsDisplay').fadeIn().html('<div class="alert alert-danger">' + response.errors + '</div>');
                        } 
                        
                    }
  
                });
                window.history.replaceState({}, '', url);
            });
        });

        tailles.each((key, element) => 
        {
            $(element).click((e) => {
                e.preventDefault();
                let url = $(element).attr('href');
                let data = $(element).text().toLowerCase();

                $.ajax(
                    {
                        method: 'post',
                        url: url,
                        data: data,
                        dataType: 'json',
                        beforeSend : () => 
                        {
                            showLoader();
                        },
                        success: (response) => 
                        {
                            if (response.success) 
                            {
                                closeLoader();
                                $('.jsProductsDisplay').fadeIn().html(response.results);
                            }
                            if (response.errors) {
                                $('.jsProductsDisplay').fadeIn().html('<div class="alert alert-danger">' + response.errors + '</div>');
                            }
                        }
                    });
                window.history.replaceState({}, '', url);
            });
        });

        couleurs.each((key, element) => 
        {
            $(element).click((e) => {
                e.preventDefault();
                let url = $(element).attr('href');
                let data = $(element).text().toLowerCase();

                $.ajax(
                    {
                        method: 'post',
                        url: url,
                        data: data,
                        dataType: 'json',
                        beforeSend : () => 
                        {
                            showLoader();
                        },
                        success: (response) => 
                        {
                            if (response.success) 
                            {
                                closeLoader();
                                $('.jsProductsDisplay').fadeIn().html(response.results);
                            }
                            if (response.errors) {
                                $('.jsProductsDisplay').fadeIn().html('<div class="alert alert-danger">' + response.errors + '</div>');
                            }
                        }
                    });
                window.history.replaceState({}, '', url);
            });
        });

        minPrice.each((key, element) => 
        {
            $(element).click((e) => {
                e.preventDefault();
                let url = $(element).attr('href');

                $.ajax(
                    {
                        method: 'post',
                        url: url,
                        dataType: 'json',
                        beforeSend : () => 
                        {
                            showLoader();
                        },
                        success: (response) => 
                        {
                            if (response.success) 
                            {
                                closeLoader();
                                $('.jsProductsDisplay').fadeIn().html(response.results);
                            }
                            if (response.errors) {
                                $('.jsProductsDisplay').fadeIn().html('<div class="alert alert-danger">' + response.errors + '</div>');
                            }
                        }
                    });
                window.history.replaceState({}, '', url);

            });
        });

        maxPrice.each((key, element) => 
        {
            $(element).click((e) => {
                e.preventDefault();
                let url = $(element).attr('href');

                $.ajax(
                    {
                        method: 'post',
                        url: url,
                        dataType: 'json',
                        beforeSend : () => 
                        {
                            showLoader();
                        },
                        success: (response) => 
                        {
                            if (response.success) 
                            {
                                closeLoader();
                                $('.jsProductsDisplay').fadeIn().html(response.results);
                            }
                            if (response.errors) {
                                $('.jsProductsDisplay').fadeIn().html('<div class="alert alert-danger">' + response.errors + '</div>');
                            }
                        }
                    });
                window.history.replaceState({}, '', url);

            });
        });

    }
    productsJsFilters();

    function passwordPlaceholder() 
    {
        $('#inscription_password_first').attr('placeholder', 'Mot de passe *');
        $('#inscription_password_second').attr('placeholder', 'Confirmation du mot de passe *');
    }
    passwordPlaceholder();

    function datePicker() 
    {
        // you may need to change this code if you are not using Bootstrap Datepicker
        $('.js-datepicker').attr('placeholder', 'jour / mois / année');
        $('.js-datepicker').datepicker({
            format: 'dd-mm-yyyy',
            language: 'fr',

        });
    }
    datePicker();

    function redirectToAvisProduct() 
    {
        $('.avisProduct__js').click((e) => 
        {
            e.preventDefault();

            $('html, body').animate(
            {
                    scrollTop: $('.spanBefore').offset().top + 130+'px'
            }, 1000);
        });
    }
    redirectToAvisProduct();

    function dashboard() {
        $('.searchDash__js').click(() => {
            $('.dashboard__modal').css({
                opacity: 1,
                pointerEvents: 'all',
            });

            $('.modal__left').css({
                transform: 'translateX(0)',
            });
        });

        $('.modal__right').click(() => {
            $('.dashboard__modal').css({
                opacity: 0,
                pointerEvents: 'none',
            });
            $('.modal__left').css({
                transform: 'translateX(-400px)',
            });
        });

        $('.userDash__js').mouseenter(() => {
            $('.dash__user__profile').css({
                opacity: 1,
                pointerEvents: 'all',
                transform: 'translateX(0)',
            });
        });
        $('.userDash__js').mouseleave(() => {
            $('.dash__user__profile').css({
                opacity: 0,
                pointerEvents: 'none',
                transform: 'translateY(-20px)',
            });
        });

    }
    dashboard();

    if ($(window).width() < 992) 
    {
        function burger() 
        {
            // click on burger
            let burgerBtn = $('#burgerLine__mobile');
            let profileBtn = $('#open__profile__user__js');
            let top = $('#top');

            burgerBtn.click(() => 
            {
                $('.navigation').toggleClass('nav__items__active');
                $(burgerBtn).toggleClass('toggleBurger__mobile');
            });

            // Apparution des liens du ménu
            let liensNav = $('.navigation li');
            liensNav.each((indice, lien) => 
            {
                if ($(lien).css.animation) 
                {
                    $(lien).css({
                        animation: ''
                    });
                } else {
                    $(lien).css({
                        animation: `liensMenuFade 1s ease forwards ${indice / 10 + 1.5}s`
                    });
                }
            });

            profileBtn.click(() => 
            {

            });

        }
        burger();

        function showSearchBar() 
        {
            $('.ico__form__mobile__js').click(() => 
            {
                $('.query__block').fadeIn();
                console.log('Bolok est muerte');
                
            });
        }
        showSearchBar();
    }

    /*window.addEventListener('scroll', function() {
        let scrolled = this.scrollY;
        console.log(scrolled);
        
    });*/


}); // Chargement du DOM