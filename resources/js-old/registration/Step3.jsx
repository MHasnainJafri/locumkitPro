import axios from "axios";
import { Tooltip as ReactTooltip } from "react-tooltip";
import React, { useEffect, useState } from "react";

function close_dive(id) {
    $("#" + id).hide(1000);
    $(".modal-backdrop").hide(1000);
}

function Step3({ user, setUser, setStep }) {
    function get_list(id) {
        setUser({ ...user, max_distance: id });
        if (id == "Over 50") {
            return;
        } else {
            var town = user.city;
            var addr = user.address + "+" + user.city + ",+UK";
            var zip = user.zip;
            var cat_id = user.profession;
            if (town != "") {
                $("#getlist-section").show();
                $("#getlist-section").addClass("in");
                $("#getlist-section").css("display", "block");
                $("#load_list").show();
                $("#store_list_div").html("");
                $.ajax({
                    url: "/ajax/get-town-list",
                    dataType: "json",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector("meta[name='csrf-token']").content,
                    },
                    type: "POST",
                    data: {
                        max_dis: id,
                        city: user.city,
                        cat_id: user.profession,
                        full_addr: addr,
                        zip: zip,
                    },
                    success: function (result) {
                        //alert(result);
                        if (result) {
                            $("#load_list").hide();
                            $("#store_list_div").html(result.html);
                            $("#getlist-section").show();
                            $("#getlist-section").addClass("in");
                            $("#getlist-section").css("display", "block");
                        }
                    },
                });
            } else {
                $(id).attr("checked", false);
            }
        }
    }

    useEffect(() => {
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
            });
    }, []);

    const validateFields = () => {
        const allRequiredSelectFields = document.querySelectorAll(`select[class*="req-qus-"]`);
        const allSelectFields = document.querySelectorAll(`select[name*="ans_val_for_question_id_"]`);
        const allRequiredCheckboxes = document.querySelectorAll(`input[class*="req-qus-"][type='checkbox']`);
        const allCheckboxes = document.querySelectorAll(`input[name*="ans_val_for_question_id_"][type='checkbox']`);
        const allOtherRequiredInputs = document.querySelectorAll(`input[class*="req-qus-"]:not([type='checkbox'])`);
        const allOtherInputs = document.querySelectorAll(`input[name*="ans_val_for_question_id_"]:not([type='checkbox'])`);

        let isVerified = true;
        const questions_answers = user.questions_answers ?? {};
        allRequiredSelectFields.forEach((element) => {
            let questionId = element.getAttribute("name").split("ans_val_for_question_id_").at(1);
            if (element.value == "") {
                document.getElementById("required-qus-" + questionId).textContent = "Please choose an option";
                document.getElementById("required-qus-" + questionId).focus();
                isVerified = false;
            } else {
                document.getElementById("required-qus-" + questionId).textContent = "";
            }
        });
        allSelectFields.forEach((element) => {
            let questionId = element.getAttribute("name").split("ans_val_for_question_id_").at(1);
            questions_answers[questionId] = element.value;
        });

        const selectedCheckboxCount = {};
        allRequiredCheckboxes.forEach((element) => {
            let questionId = element.getAttribute("data-question-id");
            if (selectedCheckboxCount[questionId]) {
                selectedCheckboxCount[questionId] = selectedCheckboxCount[questionId] + (element.checked ? 1 : 0);
            } else {
                selectedCheckboxCount[questionId] = element.checked ? 1 : 0;
            }
        });
        Object.keys(selectedCheckboxCount).forEach((questionId) => {
            if (selectedCheckboxCount[questionId] == 0) {
                document.getElementById("required-qus-" + questionId).textContent = "Please choose an option";
                isVerified = false;
            } else {
                document.getElementById("required-qus-" + questionId).textContent = "";
            }
        });

        allCheckboxes.forEach((element) => {
            let questionId = element.getAttribute("data-question-id");
            if (element.checked) {
                if (questions_answers[questionId]) {
                    questions_answers[questionId].push(element.value);
                } else {
                    questions_answers[questionId] = [element.value];
                }
            }
        });

        if (user.role == 2) {
            const requiredDaysInput = document.querySelectorAll(".req-qus-Days");
            requiredDaysInput.forEach((element) => {
                let questionId = element.getAttribute("data-question-id");
                let value = element.value;
                if (!value || !parseInt(value) || parseInt(value) <= 0) {
                    document.getElementById("required-qus-" + questionId).textContent = "Please enter a value";
                    isVerified = false;
                } else {
                    document.getElementById("required-qus-" + questionId).textContent = "";
                }
            });

            const checkedMaxDistanceNodes = document.querySelectorAll("input[name=max_distance]:checked");
            if (checkedMaxDistanceNodes.length == 0) {
                document.getElementById("store_selected_error").textContent = "Please choose an option";
                isVerified = false;
            } else {
                if (document.getElementById("store_selected_error")) {
                    document.getElementById("store_selected_error").textContent = "";
                }
            }

            if (!user.goc || user.goc.length < 2) {
                isVerified = false;
                document.getElementById("required-qus-goc-number").textContent = "Please enter correct goc number";
            } else {
                document.getElementById("required-qus-goc-number").textContent = "";
            }
        }
        let site_town_ids = null;
        try {
            site_town_ids = JSON.parse(localStorage.getItem("site_town_ids"));
        } catch (err) {}
        setUser({ ...user, questions_answers: questions_answers, site_town_ids: site_town_ids });

        if (isVerified) {
            setStep(4);
        }
    };

    const setUserRate = (rate, day) => {
        let newUser = { ...user };
        if (typeof newUser.min_rate != "object") {
            newUser.min_rate = {};
        }
        newUser.min_rate[day] = rate;
        setUser(newUser);
    };

    return (
        <div id="qus_step">
            <h3>The following questions will enable us to notify you about job opportunities relevant to your individual needs and clinical competencies.</h3>
            <div className="col-md-12 margin-bottom" align="center">
                <a id="how-to-answer-question" href={user.role == 2 ? "/how-to-answer-question-fre" : "/how-to-answer-question-emp"} target="_blank" className="tip_font2" style={{ textAlign: "center" }}>
                    (Please click here for help on how to answer these questions)
                </a>
            </div>
            {user.role == 3 && (
                <div id="emp_opt_store" className="register-frm-next-step">
                    <div className="col-md-11 register-frm" id="emp_store_list_fix">
                        <div className="col-md-6 text-right">
                            <p>What type of store do you run?</p>
                        </div>
                        <div className="col-md-6 margin-bottom">
                            <select name="store_id_emp" id="store_id_emp" className="form-control input-text width-100" defaultValue={user.store_id_emp ?? ""} onChange={(e) => setUser({ ...user, store_id_emp: e.target.value })}>
                                <option value="Boots">Boots</option>
                                <option value="Specsavers">Specsavers</option>
                                <option value="Vision express">Vision express</option>
                                <option value="Asda">Asda</option>
                                <option value="David Clulows">David Clulows</option>
                                <option value="Domaciliary">Domaciliary</option>
                                <option value="Independent">Independent</option>
                                <option value="Leightons">Leightons</option>
                                <option value="Scrivens">Scrivens</option>
                                <option value="Tesco">Tesco</option>
                                <option value="The Optical Shop">The Optical Shop</option>
                                <option value="Optical express">Optical express</option>
                            </select>
                        </div>
                    </div>
                </div>
            )}
            <div id="question_div" name="question_form" className="register-frm register-frm-next-step"></div>
            {user.role == 2 && (
                <>
                    <div id="free_min_rate" className="register-frm register-frm-next-step">
                        <div className="col-md-11" id="free_min_rate_open">
                            <div className="col-md-6 text-right">
                                <p>
                                    Please enter the minimum acceptable rate<i className="fa fa-asterisk required-stars" aria-hidden="true"></i>
                                    <br />
                                    <span className="tip_font2">( Please enter a whole number, ie: 250 )</span>
                                </p>
                            </div>
                            <div className="col-md-6">
                                <div className="col-md-12 padding-none">
                                    <div className="col-md-3 padding-none">
                                        <p className="font-weight-500">Monday</p>
                                        <input type="hidden" name="week_days_free_rate[]" value="" />
                                    </div>
                                    <div className="col-md-9 padding-none">
                                        <input
                                            type="number"
                                            name="min_rate[]"
                                            data-question-id="Monday"
                                            className="form-control req-qus-Days input-text width-100"
                                            placeholder="Enter minimum rate"
                                            value={user.min_rate && user.min_rate.monday ? user.min_rate.monday : ""}
                                            onChange={(e) => setUserRate(e.target.valueAsNumber, "monday")}
                                        />
                                        <div id="required-qus-Monday" style={{ clear: "both", color: "red" }}></div>
                                    </div>
                                </div>
                                <div className="col-md-12 padding-none">
                                    <div className="col-md-3 padding-none">
                                        <p className="font-weight-500">Tuesday</p>
                                        <input type="hidden" name="week_days_free_rate[]" value="" />
                                    </div>
                                    <div className="col-md-9 padding-none">
                                        <input
                                            type="number"
                                            name="min_rate[]"
                                            data-question-id="Tuesday"
                                            className="form-control req-qus-Days input-text width-100"
                                            placeholder="Enter minimum rate"
                                            value={user.min_rate && user.min_rate.tuesday ? user.min_rate.tuesday : ""}
                                            onChange={(e) => setUserRate(e.target.valueAsNumber, "tuesday")}
                                        />
                                        <div id="required-qus-Tuesday" style={{ clear: "both", color: "red" }}></div>
                                    </div>
                                </div>
                                <div className="col-md-12 padding-none">
                                    <div className="col-md-3 padding-none">
                                        <p className="font-weight-500">Wednesday</p>
                                        <input type="hidden" name="week_days_free_rate[]" value="" />
                                    </div>
                                    <div className="col-md-9 padding-none">
                                        <input
                                            type="number"
                                            name="min_rate[]"
                                            data-question-id="Wednesday"
                                            className="form-control req-qus-Days input-text width-100"
                                            placeholder="Enter minimum rate"
                                            value={user.min_rate && user.min_rate.wednesday ? user.min_rate.wednesday : ""}
                                            onChange={(e) => setUserRate(e.target.valueAsNumber, "wednesday")}
                                        />
                                        <div id="required-qus-Wednesday" style={{ clear: "both", color: "red" }}></div>
                                    </div>
                                </div>
                                <div className="col-md-12 padding-none">
                                    <div className="col-md-3 padding-none">
                                        <p className="font-weight-500">Thursday</p>
                                        <input type="hidden" name="week_days_free_rate[]" value="" />
                                    </div>
                                    <div className="col-md-9 padding-none">
                                        <input
                                            type="number"
                                            name="min_rate[]"
                                            data-question-id="Thursday"
                                            className="form-control req-qus-Days input-text width-100"
                                            placeholder="Enter minimum rate"
                                            value={user.min_rate && user.min_rate.thursday ? user.min_rate.thursday : ""}
                                            onChange={(e) => setUserRate(e.target.valueAsNumber, "thursday")}
                                        />
                                        <div id="required-qus-Thursday" style={{ clear: "both", color: "red" }}></div>
                                    </div>
                                </div>
                                <div className="col-md-12 padding-none">
                                    <div className="col-md-3 padding-none">
                                        <p className="font-weight-500">Friday</p>
                                        <input type="hidden" name="week_days_free_rate[]" value="" />
                                    </div>
                                    <div className="col-md-9 padding-none">
                                        <input
                                            type="number"
                                            name="min_rate[]"
                                            data-question-id="Friday"
                                            className="form-control req-qus-Days input-text width-100"
                                            placeholder="Enter minimum rate"
                                            value={user.min_rate && user.min_rate.friday ? user.min_rate.friday : ""}
                                            onChange={(e) => setUserRate(e.target.valueAsNumber, "friday")}
                                        />
                                        <div id="required-qus-Friday" style={{ clear: "both", color: "red" }}></div>
                                    </div>
                                </div>
                                <div className="col-md-12 padding-none">
                                    <div className="col-md-3 padding-none">
                                        <p className="font-weight-500">Saturday</p>
                                        <input type="hidden" name="week_days_free_rate[]" value="" />
                                    </div>
                                    <div className="col-md-9 padding-none">
                                        <input
                                            type="number"
                                            name="min_rate[]"
                                            data-question-id="Saturday"
                                            className="form-control req-qus-Days input-text width-100"
                                            placeholder="Enter minimum rate"
                                            value={user.min_rate && user.min_rate.saturday ? user.min_rate.saturday : ""}
                                            onChange={(e) => setUserRate(e.target.valueAsNumber, "saturday")}
                                        />
                                        <div id="required-qus-Saturday" style={{ clear: "both", color: "red" }}></div>
                                    </div>
                                </div>
                                <div className="col-md-12 padding-none">
                                    <div className="col-md-3 padding-none">
                                        <p className="font-weight-500">Sunday</p>
                                        <input type="hidden" name="week_days_free_rate[]" value="" />
                                    </div>
                                    <div className="col-md-9 padding-none">
                                        <input
                                            type="number"
                                            name="min_rate[]"
                                            data-question-id="Sunday"
                                            className="form-control req-qus-Days input-text width-100"
                                            placeholder="Enter minimum rate"
                                            value={user.min_rate && user.min_rate.sunday ? user.min_rate.sunday : ""}
                                            onChange={(e) => setUserRate(e.target.valueAsNumber, "sunday")}
                                        />
                                        <div id="required-qus-Sunday" style={{ clear: "both", color: "red" }}></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="storeinfo_div">
                        <div className="register-frm register-frm-next-step">
                            <div className="col-md-11 lk_dst_radio">
                                <div className="col-md-6 margin-bottom text-right">
                                    <p className="lk_dst_txt" style={{ marginBottom: 0 }}>
                                        How far you willing to travel?<i className="fa fa-asterisk required-stars" aria-hidden="true"></i>
                                    </p>
                                    <p className="lk_dst_txt">
                                        <em style={{ fontStyle: "italic", fontSize: "12px" }}>
                                            <a style={{ color: "#00a8dd" }} href="/maps">
                                                Click here
                                            </a>
                                            to view a map of UK boroughs/counties.
                                        </em>
                                    </p>
                                </div>
                                <div className="col-md-6">
                                    <div className="dist_list">
                                        <input type="radio" name="max_distance" className="width-100 margin-right" checked={user && user.max_distance == "5" ? true : false} value="5" onClick={(e) => get_list(e.target.value)} />
                                        <span>5 miles</span>
                                    </div>
                                    <div className="dist_list">
                                        <input type="radio" name="max_distance" className="width-100 margin-right" checked={user && user.max_distance == "10" ? true : false} value="10" onClick={(e) => get_list(e.target.value)} />
                                        <span>10 miles</span>
                                    </div>
                                    <div className="dist_list">
                                        <input type="radio" name="max_distance" className="width-100 margin-right" checked={user && user.max_distance == "15" ? true : false} value="15" onClick={(e) => get_list(e.target.value)} />
                                        <span>15 miles</span>
                                    </div>
                                    <div className="dist_list">
                                        <input type="radio" name="max_distance" className="width-100 margin-right" checked={user && user.max_distance == "20" ? true : false} value="20" onClick={(e) => get_list(e.target.value)} />
                                        <span>20 miles</span>
                                    </div>
                                    <div className="dist_list">
                                        <input type="radio" name="max_distance" className="width-100 margin-right" checked={user && user.max_distance == "25" ? true : false} value="25" onClick={(e) => get_list(e.target.value)} />
                                        <span>25 miles</span>
                                    </div>
                                    <div className="dist_list">
                                        <input type="radio" name="max_distance" className="width-100 margin-right" checked={user && user.max_distance == "30" ? true : false} value="30" onClick={(e) => get_list(e.target.value)} />
                                        <span>30 miles</span>
                                    </div>
                                    <div className="dist_list">
                                        <input type="radio" name="max_distance" className="width-100 margin-right" checked={user && user.max_distance == "35" ? true : false} value="35" onClick={(e) => get_list(e.target.value)} />
                                        <span>35 miles</span>
                                    </div>
                                    <div className="dist_list">
                                        <input type="radio" name="max_distance" className="width-100 margin-right" checked={user && user.max_distance == "40" ? true : false} value="40" onClick={(e) => get_list(e.target.value)} />
                                        <span>40 miles</span>
                                    </div>
                                    <div className="dist_list">
                                        <input type="radio" name="max_distance" className="width-100 margin-right" checked={user && user.max_distance == "45" ? true : false} value="45" onClick={(e) => get_list(e.target.value)} />
                                        <span>45 miles</span>
                                    </div>
                                    <div className="dist_list">
                                        <input type="radio" name="max_distance" className="width-100 margin-right" checked={user && user.max_distance == "50" ? true : false} value="50" onClick={(e) => get_list(e.target.value)} />
                                        <span>50 miles</span>
                                    </div>
                                    <div className="dist_list">
                                        <input type="radio" name="max_distance" className="width-100 margin-right" checked={user && user.max_distance == "Over 50" ? true : false} value="Over 50" onClick={(e) => get_list(e.target.value)} />
                                        <span>Over 50 miles</span>
                                    </div>
                                    <div id="store_selected_error" style={{ clear: "both", marginBottom: "10px" }}></div>
                                </div>
                            </div>
                            <div className="col-md-11">
                                <div className="celltip-bmm regist-tip">
                                    <div className="col-md-6 celltip-wppr text-right">
                                        <p>
                                            How many CET points do you have in the current cycle
                                            <p
                                                id="my-cet-element"
                                                style={{ display: "inline", paddingInlineStart: "10px", cursor: "pointer" }}
                                                data-tooltip-content="Locumkit will require evidence of your CET points every three months to ensure credibility."
                                            >
                                                <i className="fa fa-question-circle" aria-hidden="true"></i>
                                            </p>
                                        </p>
                                    </div>
                                    <div className="col-md-6 text-left">
                                        <input
                                            type="number"
                                            name="cet"
                                            id="cet"
                                            className="form-control input-text width-100"
                                            placeholder="000"
                                            maxLength={3}
                                            autoComplete="off"
                                            style={{ margin: "10px 0px" }}
                                            value={user.cet ?? ""}
                                            onChange={(e) => setUser({ ...user, cet: e.target.value.slice(0, 3) })}
                                        />
                                    </div>
                                </div>
                            </div>
                            <div className="col-md-11">
                                <div className="col-md-6 text-right">
                                    <p>
                                        What is your GOC NO?<i className="fa fa-asterisk required-stars" aria-hidden="true"></i>
                                    </p>
                                </div>
                                <div className="col-md-6 text-left">
                                    <input
                                        type="text"
                                        name="goc"
                                        id="goc"
                                        className="form-control input-text width-100 "
                                        placeholder="01-12345 or D-00000"
                                        maxLength={8}
                                        autoComplete="off"
                                        value={user.goc ?? ""}
                                        onChange={(e) => setUser({ ...user, goc: e.target.value.slice(0, 8) })}
                                    />
                                    <div id="required-qus-goc-number"></div>
                                </div>
                            </div>
                            <div className="col-md-11">
                                <div className="col-md-6 text-right">
                                    <p>Indemnity insurance: If AOP, what is your AOP membership number? </p>
                                </div>
                                <div className="col-md-6">
                                    <input
                                        type="text"
                                        name="aop"
                                        id="aop"
                                        className="form-control input-text width-100 "
                                        placeholder="00000"
                                        maxLength={5}
                                        autoComplete="off"
                                        style={{ margin: "10px 0px" }}
                                        value={user.aop ?? ""}
                                        onChange={(e) => setUser({ ...user, aop: e.target.value.slice(0, 5) })}
                                    />
                                </div>
                            </div>
                            <div className="col-md-11">
                                <div className="col-md-6 text-right">
                                    <p> If not AOP, please insert Company insured with, Policy number, Date of renewal? </p>
                                </div>
                                <div className="col-md-6">
                                    <p>
                                        <input type="checkbox" name="inshu_yes" id="inshu_yes" checked={user.inshu_yes} onChange={(e) => setUser({ ...user, inshu_yes: e.target.checked })} className="width-100 pull-left margin-bottom" />
                                        <span className="margin-left lk_confirm text-left">Yes</span>
                                    </p>
                                    {user && user.inshu_yes && (
                                        <div id="inshu_open" className="margin-top">
                                            <input
                                                type="text"
                                                name="inshurance_company"
                                                id="inshurance_company"
                                                className="form-control input-text width-100 margin-bottom"
                                                placeholder="company name"
                                                autoComplete="off"
                                                value={user.inshurance_company ?? ""}
                                                onChange={(e) => setUser({ ...user, inshurance_company: e.target.value })}
                                            />
                                            <input
                                                type="text"
                                                name="inshurance_no"
                                                id="inshurance_no"
                                                className="form-control input-text width-100 margin-bottom"
                                                placeholder="Membership number"
                                                autoComplete="off"
                                                value={user.inshurance_no ?? ""}
                                                onChange={(e) => setUser({ ...user, inshurance_no: e.target.value })}
                                            />
                                            <input
                                                type="date"
                                                name="inshurance_renewal_date"
                                                id="inshurance_renewal_date"
                                                className="form-control input-text width-100 margin-bottom"
                                                placeholder="dd-mm-yyyy"
                                                autoComplete="off"
                                                value={user.inshurance_renewal_date ?? ""}
                                                onChange={(e) => setUser({ ...user, inshurance_renewal_date: e.target.value })}
                                            />
                                        </div>
                                    )}
                                </div>
                            </div>
                            <div className="col-md-11">
                                <div className="col-md-6 margin-bottom text-right">
                                    <p>What is your Opthalmic List Number (OPL)?</p>
                                </div>
                                <div className="col-md-6">
                                    <input
                                        type="text"
                                        name="aoc_id"
                                        id="aoc_id"
                                        placeholder="OPL 11-11111/1AA"
                                        maxLength={16}
                                        className="form-control input-text width-100"
                                        style={{ margin: "10px 0px" }}
                                        value={user.aoc_id ?? ""}
                                        onChange={(e) => setUser({ ...user, aoc_id: e.target.value.slice(0, 16) })}
                                    />
                                    <div id="-required-qus-10002"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="getlist-section" className="modal fade" role="dialog">
                        <div className="list-popup">
                            <div className="modal-dialog" style={{ overflowY: "auto", height: "calc(100vh - 8rem)" }}>
                                <div className="modal-content">
                                    <div className="modal-header no-border-bottom">
                                        <button type="button" className="close" data-dismiss="modal" onClick={(e) => close_dive("getlist-section")}>
                                            ×
                                        </button>
                                        <h4 className="modal-title">Towns list</h4>
                                    </div>
                                    <div className="modal-body">
                                        <h3 id="load_list" style={{ display: "none" }}>
                                            <img src="/frontend/locumkit-template/img/loader.gif" /> Please wait...
                                        </h3>
                                        <div id="store_list_div"></div>
                                    </div>
                                    <div className="modal-footer no-border-top"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </>
            )}
            <div className="col-md-12 mx-3 pad0 form-group text-center" style={{ marginTop: "20px" }}>
                <span className="formlft">
                    <button type="button" className="btn btn-default btn-1 lkbtn" id="back_qus" onClick={() => setStep(2)}>

                    </button>
                </span>
                <span className="formlft mx-3" style={{ margin: "30px 0px" }} id="payment-pro">
                    <button type="button" className="btn btn-default btn-1 lkbtn" onClick={(_) => validateFields()}>
                        <span>
                            Next <i className="fa fa-angle-double-right" aria-hidden="true"></i>
                        </span>
                    </button>
                </span>
                {/* <span id="normal-submit" style={{ display: "none" }}>
                    <button type="button" className="btn btn-default btn-1 lkbtn" id="save_user" onClick="save_ans();">
                        <span>
                            submit &nbsp;&nbsp;<i className="fa fa-angle-double-right" aria-hidden="true"></i>
                        </span>
                    </button>
                </span> */}
            </div>

            <ReactTooltip style={{ maxWidth: "300px" }} anchorId="my-cet-element" />
        </div>
    );
}

export default Step3;
