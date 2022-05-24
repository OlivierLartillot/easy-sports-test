const app = {
    init:function()
    {
        const selectTeam = document.getElementById('result_team');
        selectTeam.addEventListener('change',app.handleChangeTeam);
        const userSelect = document.getElementById('result_user');
        userSelect.addEventListener('change',app.handleChangeUser);

    },

    handleChangeTeam:function(evt)
    {   
        const userSelect = document.getElementById('result_user');
        userSelect.innerHTML = '<option>Choisissez un joueur</option>'
        //let none = document.querySelectorAll('.d-none');
        const idTeam = document.getElementById('result_team').value;
        let fetchOptions = {
            method: 'GET',
            mode:   'cors',
            cache:  'no-cache'
        };
        fetch('http://localhost:8080/teamFromResult/' + idTeam, fetchOptions)
            .then(function (response) {
            if (response.ok) {
                return response.json();
            }
            return Promise.reject(response);
        }).then(function (data) {
            console.log(data.name);
            for(key in data){
                let d = data[key];
                console.log(d);
                let option = document.createElement('option');
                option.value = d.id;
                option.text = d.firstname;
                userSelect.append(option);
            }
            
        }).catch(function (error) {
            console.log("le message d'erreur", error);
        });
            
        userSelect.classList.remove('d-none');
        const label = document.querySelector('.user_label')
        label.classList.remove('d-none')
    },

    handleChangeUser:function(evt)
    {
        const resultSelect = document.getElementById('result_result');
        resultSelect.classList.remove('d-none');
        const label = document.querySelector('.result_label')
        label.classList.remove('d-none')
    }
    

    

}

app.init();