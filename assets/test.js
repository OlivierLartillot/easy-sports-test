import { add } from "@hotwired/stimulus";

const test = {
    init:function()
    {
        const selectTest = document.getElementById('result_current_user_test');
        selectTest.addEventListener('change',test.handleChangeTest)
    },

    handleChangeTest:function(evt)
    {   
        const nameTest = document.querySelector('.name_test')
        const imgTest = document.getElementById('test_img')
        const videoTest = document.getElementById('test_video')
        const descriptionTest = document.getElementById('description_test')
        const selectedValue = document.getElementById('result_current_user_test').value;
        let fetchOptions = {
            method: 'GET',
            mode:   'same-origin',
            cache:  'no-cache'
        };
        fetch('http://localhost:8080/testFromResult/' + selectedValue, fetchOptions)
            .then(function (response) {
            if (response.ok) {
                return response.json();
            }
            return Promise.reject(response);
            }).then(function (data) {
                console.log(data);
                for(let test in data){
                    nameTest.textContent = 'Test : '+data[test].name+' (en '+ data[test].unit+')';
                    descriptionTest.textContent = data[test].description;
                    videoTest.src = ''
                    videoTest.alt = 'no video'
                    imgTest.src=''
                    imgTest.alt='no image'
                    if(!videoTest.classList.contains('d-none')){
                        videoTest.classList.add('d-none');
                    }
                    if(!imgTest.classList.contains('d-none')){
                        videoTest.classList.add('d-none');
                    }                
                    if(data[test].media != null){
                        if(data[test].media.endsWith('mp4')){
                            videoTest.classList.remove('d-none')
                            videoTest.src = '../uploads/images/tests/' + data[test].media
                            videoTest.alt = data[test].name
                        }else{
                            imgTest.classList.remove('d-none')
                            imgTest.src = '../uploads/images/tests/' + data[test].media
                            imgTest.alt = data[test].name
                        }
                    
                    }
                
                }
            
            }).catch(function (error) {
                console.log("le message d'erreur", error);
            });
    }
}

test.init();
