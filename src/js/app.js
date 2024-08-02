$(function show() {
    /*CREATE ACCOUNT*/
    $(".createAccount").click(function (e) {
        createForm = `
    <div class="updatForm bg-success">
    <div class="addbooks">Create account</div>   
    <form method="POST" action="createmember" id="createUserForm">
        <div class="input-group mb-2">
            <span class="input-group-text bg-dark" id="inputGroup-sizing-default">User Email</span>
            <input type="email" name="user_email" class="form-control"
                aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" required>
        </div>
        <div class="input-group mb-2">
            <span class="input-group-text bg-dark" id="inputGroup-sizing-default">User Role</span>
            <input type="text" name="user_role" class="form-control"
                aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" required>
        </div>
        <div class="input-group mb-2">
            <span class="input-group-text bg-dark" id="inputGroup-sizing-default">User Password</span>
            <input type="password" name="user_password" class="form-control"
                aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" required>
        </div>
        <div class="input-group mb-2">
            <input type="submit" class="form-control bg-warning fw-bold" aria-label="Sizing example input"
                aria-describedby="inputGroup-sizing-default" name="createUserData" value="Create account">
        </div>
  </form>
  </div>
        `;

        var layout = document.querySelector('.layout')
        layout.classList.toggle("d-none")
        layout.innerHTML = createForm;
    })


    /*MANAGE TASK : addtask*/
    $("form.formAddTask select.selectProject").on("change", function (e) {
        console.log(e.target.value);
        var projectSelectedName = e.target.value
        //affiche les id selon le projet selectionne
        var memberEmail = $("a strong.emailMember").html()

        const url_m = location.origin + "/pmsysteme/src/json/" + memberEmail + "m.json"
        const url_p = location.origin + "/pmsysteme/src/json/" + memberEmail + "p.json"
        const url_mb = location.origin + "/pmsysteme/src/json/" + memberEmail + "mb.json"

        $("form.formAddTask select.selectMember option").html("")
        $("form.formAddTask select.selectMember").html("<option>choose member</option>")

        $.ajax({
            url: url_p,
            // type: "POST",
            success: function (response, status, xhr) {
                if (status == "success") {
                    const projectData = response
                    console.log(projectData);

                    $.ajax({
                        url: url_mb,
                        // type: "POST",
                        success: function (response, status, xhr) {
                            if (status == "success") {
                                const membersData = response
                                console.log(membersData);

                                for (let i = 0; i < projectData.length; i++) {
                                    const element_i = projectData[i];
                                    if (element_i["name"] == projectSelectedName) {
                                        for (let j = 0; j < membersData.length; j++) {
                                            const element_j = membersData[j];
                                            if (element_i["member_id"] == element_j["id"]) {
                                                $("form.formAddTask select.selectMember").append(`<option>${element_j["email"]}</option>`)
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    )
                }
            }
        }
        )
        // $("form.formAddTask select.selectMember option").html("ddddd")
    })


    /*DASHBOARD*/
    $(".fa-circle-plus").on("click", function (e) {
        console.log(e.target.id);
        var idToShow = e.target.id

        var idTarget = document.getElementsByClassName(idToShow)
        for (let index = 0; index < idTarget.length; index++) {
            const element = idTarget[index];
            element.classList.toggle("d-none")

        }
    })

})