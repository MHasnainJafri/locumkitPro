import React, { useLayoutEffect } from "react";

function Step4Locum({ user, setUser, setStep }) {
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
        <div>
            <div>
                <form action={REGISTER_FORM_URI} method="post" id="my-submit-form">
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
                        <input type="hidden" name="company" value={user.company} readOnly />
                        <input type="hidden" name="address" value={user.address} readOnly />
                        <input type="hidden" name="city" value={user.city} readOnly />
                        <input type="hidden" name="zip" value={user.zip} readOnly />
                        <input type="hidden" name="telephone" value={user.telephone} readOnly />
                        <input type="hidden" name="mobile" value={user.mobile} readOnly />
                        <input type="hidden" name="g_recaptcha_response" value={user.g_recaptcha_response} readOnly />
                        <input type="hidden" name="min_rate[]" value={user.min_rate.monday} readOnly />
                        <input type="hidden" name="min_rate[]" value={user.min_rate.tuesday} readOnly />
                        <input type="hidden" name="min_rate[]" value={user.min_rate.wednesday} readOnly />
                        <input type="hidden" name="min_rate[]" value={user.min_rate.thursday} readOnly />
                        <input type="hidden" name="min_rate[]" value={user.min_rate.friday} readOnly />
                        <input type="hidden" name="min_rate[]" value={user.min_rate.saturday} readOnly />
                        <input type="hidden" name="min_rate[]" value={user.min_rate.sunday} readOnly />
                        <input type="hidden" name="max_distance" value={user.max_distance} readOnly />
                        <input type="hidden" name="cet" value={user.cet} readOnly />
                        <input type="hidden" name="goc" value={user.goc} readOnly />
                        <input type="hidden" name="aop" value={user.aop} readOnly />
                        <input type="hidden" name="inshurance_company" value={user.inshurance_company} readOnly />
                        <input type="hidden" name="inshurance_no" value={user.inshurance_no} readOnly />
                        <input type="hidden" name="inshurance_renewal_date" value={user.inshurance_renewal_date} readOnly />
                        <input type="hidden" name="aoc_id" value={user.aoc_id} readOnly />
                        {user && user.site_town_ids && typeof user.site_town_ids == "object" && user.site_town_ids.length > 0 && user.site_town_ids.map((town_id) => <input key={town_id} type="hidden" name="store_list[]" value={town_id} />)}
                        <div id="question_div"></div>
                    </div>

                    <section id="packages" className="package">
                        <div className="package-container">
                            <div className="package-block">
                                <div className="col-md-3 col-sm-3 package-price-box" id="package-4">
                                    <div className="set-pack-icon">
                                        <div className="set-pack-price">
                                            <img src="/frontend/locumkit-template/img/logo.png" title="Locumkit" alt="Locumkit" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <div className="col-md-12 btn-img paypalbtnn">
                        <button type="button" style="margin-right:8px;" className="btn btn-default btn-1 lkbtn"  onClick={(_) => setStep(3)}>
                            <span>
                                <i className="fa fa-angle-double-left" aria-hidden="true"></i> Back
                            </span>
                        </button>
                        <button type="submit" className="btn btn-default btn-1 lkbtn">
                            <span>
                                Continue &nbsp;&nbsp;<i className="fa fa-angle-double-right" aria-hidden="true"></i>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    );
}

export default Step4Locum;
