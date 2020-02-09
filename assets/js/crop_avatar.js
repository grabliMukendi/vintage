
import Cropper from 'cropperjs';
import Axios from 'axios';
//const axios = require('axios').default;
import Routing from '../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';
import Routes from '../../public/js/fos_js_routes.json';
Routing.setRoutingData(Routes);

document.addEventListener('DOMContentLoaded', () => 
{
    let fileInput = document.getElementById('profil_userAdress_avatarFile');
    let preview = document.getElementById('avatar__cropper__js');
    let cropper;
    
    fileInput.addEventListener('change', (e) => 
    {
        $('#avatarModal').modal('show');
    
        setTimeout(() =>
        {
            let reader = new FileReader();
            reader.onload = (event) => 
            {
                preview.src = reader.result;
            }
            reader.readAsDataURL(e.target.files[0]);
    
            preview.addEventListener('load', (e) => 
            {
                cropper = new Cropper(preview,
                {
                    aspectRatio: 9/9,
                });
            });
    
            let cropBtn = document.getElementById('crop__save__js');
            cropBtn.addEventListener('click', () => 
            {
                cropper.getCroppedCanvas(
                {
                    maxHeight: 1000,
                    maxWidth: 1000,
                })
                .toBlob((blob) => 
                {
                    ajaxWithAxios(blob);
                }/*, 'image/jpeg'*/);
            });
            
            function ajaxWithAxios(blob) 
            {
                let url = Routing.generate('image');
                let data = new FormData(document.querySelector('form[name="profil"]'));
                data.append('file', blob);
                
                Axios(
                {
                    method: 'POST',
                    url: url,
                    data: data,
                    headers: { 'X-Requested-With' : 'XMLHttpRequest' },
                }).then((response) => 
                {
                    console.log(response);
                    $('#avatarModal').modal('hide');
                    location.assign('');
                    
                }).catch((errors) => 
                {
                    console.log(errors);
                    
                });
            }
    
        }, 200);
    
    });
});