import React, { useLayoutEffect } from "react";
import { Tooltip } from "react-tooltip";

function Step4Employer({ user, setUser, setStep }) {
    const addNewStore = () => {
        $.ajax({
            url: "/ajax/mutli-store-time",
            type: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector("meta[name='csrf-token']").content,
            },
            success: function (result) {
                $(".store_block").append(
                    `
                    <div class="store-details add-new-store-inner-scroll">
                        <div class="col-xs-3 col-sm-3 col-md-3 no-padding-left">
                            <input type="text" name="emp_store_name_${result.key}" required placeholder="Enter Store name" class="form-control input-text width-100 required-field_${result.key}">
                        </div>
                        <div class="col-xs-4 col-sm-4 col-md-4 no-padding-left">
                            <input type="text" name="emp_store_address_${result.key}" required placeholder="Enter Store address" class="form-control input-text width-100 required-field_${result.key}">
                        </div>
                        <div class="col-xs-2 col-sm-2 col-md-2 no-padding-left">
                            <input type="text" name="emp_store_region_${result.key}" required placeholder="Enter Store Region" class="emp_store_region form-control input-text width-100 required-field_${result.key} city">
                        </div>
                        <div class="col-xs-2 col-sm-2 col-md-2 no-padding-left">
                            <input type="text" name="emp_store_zip_${result.key}" required placeholder="Enter Store post code" class="form-control input-text width-100 required-field_${result.key}">
                        </div>
                        <span class="removeclass small2" onclick="$(this).parent('.store-details').remove()"><i class="fa fa-times" title="Remove" aria-hidden="true"></i></span>
                        <div class="css_error2 required-field-no_${result.key}" style="clear:both;"> </div>
                        ${result.html}
                    </div>
                    `
                );
                $(".emp_store_region").autocomplete({
                    source: AVAILABLE_TAGS,
                });
            },
        });
    };

    useLayoutEffect(() => {
        $.ajax({
            url: "/ajax/mutli-store-time",
            type: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector("meta[name='csrf-token']").content,
            },
            success: function (result) {
                $("#set_user_data").html(
                    `<div class="col-md-3 no-padding-left margin-bottom"><input type="text" name="emp_store_name_${result.key}" placeholder="Enter Store name" class="form-control input-text width-100" value="` +
                        user.store +
                        `" ></div><div class="col-md-3 no-padding-left"><input type="text" name="emp_store_address_${result.key}" placeholder="Enter Store address" class="form-control input-text width-100" value="` +
                        user.address +
                        `"></div><div class="col-md-3 no-padding-left"><input type="text" name="emp_store_region_${result.key}" placeholder="Enter Store town" class="emp_store_region form-control input-text width-100" value="` +
                        user.city +
                        `" ></div><div class="col-md-2 no-padding-left"><input type="text" name="emp_store_zip_${result.key}" placeholder="Enter Store postcode" class="form-control input-text width-100" value="` +
                        user.zip +
                        '" ></div>' +
                        result.html
                );
                $(".emp_store_region").autocomplete({
                    source: AVAILABLE_TAGS,
                });
            },
        });
    }, []);

    useLayoutEffect(() => {
        axios
            .post(
                "/ajax/question-by-role",
                {
                    cat_id: user.profession,
                    role_id: user.role,
                },
                {
                    headers: {
                        "X-CSRF-TOKEN": CSRF_TOKEN,
                    },
                }
            )
            .then((res) => {
                $("#question_div").html(res.data.html);
                const allSelectFields = document.querySelectorAll(`select[name*="ans_val_for_question_id_"]`);
                const allCheckboxes = document.querySelectorAll(`input[name*="ans_val_for_question_id_"][type='checkbox']`);

                allSelectFields.forEach((element) => {
                    let questionId = element.getAttribute("name").split("ans_val_for_question_id_").at(1);
                    if (user && typeof user.questions_answers == "object" && user.questions_answers[questionId]) {
                        element.value = user.questions_answers[questionId];
                    }
                });

                allCheckboxes.forEach((element) => {
                    let questionId = element.getAttribute("data-question-id");
                    if (user && typeof user.questions_answers == "object" && user.questions_answers[questionId]) {
                        let arr = user.questions_answers[questionId];
                        if (arr.includes(element.value)) {
                            element.setAttribute("checked", true);
                        }
                    }
                });

                $("#my-submit-form [required]").prop("required", false);
            });
    }, []);

    return (
        <div id="emp-store-div">
            <form action={REGISTER_FORM_URI} method="post" id="my-submit-form">
                <div className="col-md-12">
                    <h2 className="reg-step-title text-center">
                        Add Store Information
                        <span id="my-tooltip-info">
                            <i className="fa fa-question-circle" aria-hidden="true"></i>
                        </span>
                    </h2>
                </div>
                <div className="col-md-12">
                    <div className="store_block">
                        <div className="store-details">
                            <div className="width-full" id="show_add_button">
                                <a href="#" className="color-blue float-right" onClick={(_) => addNewStore()}>
                                    <i className="fa fa-plus" aria-hidden="true" title="Add Employer store"></i>
                                </a>
                            </div>
                            <div className="margin-bottom" id="set_user_data"></div>
                        </div>
                    </div>
                </div>

                <div style={{ display: "none" }}>
                    <input type="hidden" name="_token" value={CSRF_TOKEN} readOnly />
                    <input type="hidden" name="role" value={user.role} readOnly />
                    <input type="hidden" name="profession" value={user.profession} readOnly />
                    <input type="hidden" name="fname" value={user.firstname} readOnly />
                    <input type="hidden" name="lname" value={user.lastname} readOnly />
                    <input type="hidden" name="email" value={user.email} readOnly />
                    <input type="hidden" name="login" value={user.username} readOnly />
                    <input type="hidden" name="password" value={user.password} readOnly />
                    <input type="hidden" name="password_confirmation" value={user.password_confirmation} readOnly />
                    <input type="hidden" name="company" value={user.store} readOnly />
                    <input type="hidden" name="address" value={user.address} readOnly />
                    <input type="hidden" name="city" value={user.city} readOnly />
                    <input type="hidden" name="zip" value={user.zip} readOnly />
                    <input type="hidden" name="telephone" value={user.telephone} readOnly />
                    <input type="hidden" name="mobile" value={user.mobile} readOnly />
                    <input type="hidden" name="g_recaptcha_response" value={user.g_recaptcha_response} readOnly />
                    <input type="hidden" name="store_id_emp" value={user.store_id_emp} readOnly />
                    <div id="question_div"></div>
                </div>

                <div className="col-md-12 btn-img bck_sub_div">
                    <p style={{ paddingTop: "15px" }}>
                        <span>
                            <button type="button" className="btn btn-default btn-1 lkbtn" onClick={(_) => setStep(3)}>
                                <span>
                                    <i className="fa fa-angle-double-left" aria-hidden="true"></i> Back
                                </span>
                            </button>
                        </span>
                        <span>
                            <button type="submit" className="btn btn-default btn-1 lkbtn">
                                <span>
                                    <i className="fa fa-floppy-o" aria-hidden="true"></i> Submit
                                </span>
                            </button>
                        </span>
                    </p>
                </div>
            </form>

            <Tooltip anchorId="my-tooltip-info" content="Please add store information to get the best freelancer." />
        </div>
    );
}
export { Step4Employer };

export default Step4Employer;
