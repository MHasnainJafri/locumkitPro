import React, { useRef, useState } from "react";

function Step1({ user, setUser, setStep }) {
    const roleRef = useRef();
    const professionRef = useRef();

    const [roleError, setRoleError] = useState("");
    const [professionError, setProfessionError] = useState("");

    const handleNextStep = () => {
        setRoleError("");
        setProfessionError("");
        let role = roleRef.current.value;
        let profession = professionRef.current.value;

        if (role && role > 0 && profession && profession > 0) {
            //Goto next step
            setUser({
                ...user,
                role: role,
                profession: profession,
            });

            setStep(2);
            return;
        }

        if (!role) {
            setRoleError("Please choose a role");
        }
        if (!profession) {
            setProfessionError("Please choose a profession");
        }
    };

    return (
        <>
            <div id="step1" className="col-md-12 col-sm-12 col-xs-12 regstepform formlft animate zoomIn" data-anim-type="zoomIn" data-anim-delay="300">
                <div className="form-group">
                    <div className="col-md-5 col-sm-5 col-xs-12 lft text-right">
                        <label htmlFor="name">
                            Who are you?<i className="fa fa-asterisk required-stars required-stars" aria-hidden="true"></i>
                        </label>
                    </div>
                    <div className="col-md-7 col-sm-7 col-xs-12 rgt">
                        <select name="role" className="form-control input-text width-100" defaultValue={user.role ?? ""} id="user_type" ref={roleRef}>
                            <option value="">Select</option>
                            {roles.map((role) => (
                                <option key={role.id} value={role.id}>
                                    I am {role.name}
                                </option>
                            ))}
                        </select>
                        {roleError && <div className="css_error">{roleError}</div>}
                    </div>
                </div>
                <div className="form-group">
                    <div className="col-md-5 col-sm-7 col-xs-12 lft text-right">
                        <label htmlFor="name">
                            Please select your profession?<i className="fa fa-asterisk required-stars required-stars" aria-hidden="true"></i>
                        </label>
                    </div>
                    <div className="col-md-7 col-sm-7 col-xs-12 rgt">
                        <select className="form-control input-text width-100" name="profession" defaultValue={user.profession ?? ""} id="profession_type" ref={professionRef}>
                            <option value="">Select</option>
                            {professions.map((profession) => (
                                <option key={profession.id} value={profession.id}>
                                    {profession.name}
                                </option>
                            ))}
                        </select>
                        {professionError && <div className="css_error">{professionError}</div>}
                    </div>
                </div>
                <div className="form-group text-right">
                    <button type="button" className="btn btn-default btn-1 lkbtn" id="personal_info" onClick={handleNextStep}>
                        <span>Next</span>
                    </button>
                </div>
                <div id="qus_ans_div"></div>
            </div>
            <div className="col-md-12 col-sm-12 col-xs-12 pad0 regsc2 info-benifit-section" id="info-benifit-section">
                <div className="col-md-6 col-sm-6 col-xs-12 lft">
                    <article className="animate zoomIn" data-anim-type="zoomIn" data-anim-delay="800">
                        <h3>For locums</h3>
                        <p>Being a locum can be tough. It is hard enough trying to ensure regular work without having to chase employers for payment or deal with double bookings.</p>
                        <ul className="list-inline">
                            <a href="/locums" type="button" className="btn btn-default btn-1 lkbtn">
                                <span>Learn More</span>
                            </a>
                        </ul>
                        <h3>For employers</h3>
                        <p>Using locums can be expensive and with additional agency fees, costs can start to escalate. While there are many locums out there, finding suitable and reputable locums can be a challenge.</p>
                        <ul className="list-inline">
                            <a href="/employer" type="button" className="btn btn-default btn-1 lkbtn">
                                <span>Learn More</span>
                            </a>
                        </ul>
                    </article>
                </div>
                <div className="col-md-6 col-sm-6 col-xs-12 rgt animate fadeInRight" data-anim-type="fadeInRight" data-anim-delay="800">
                    <h3>BENEFITS</h3>
                    <ul className="carretlist arrowlist">
                        <li>A dashboard view of how you are doing</li>
                        <li>Automated invoices</li>
                        <li>Manage your locum bookings 24 hours a day, 7 days a week</li>
                        <li>Receive instant notifications</li>
                        <li>Available as an app through Android and iOS</li>
                        <li>Review all finances in one place</li>
                    </ul>
                    <a href="/benefits" type="button" className="btn btn-default btn-1 lkbtn">
                        <span>Learn More</span>
                    </a>
                </div>
            </div>

            <div className="col-md-12 col-sm-12 col-xs-12 regsc3 animate register-slider fadeInUp" data-anim-type="fadeInUp" data-anim-delay="800" id="screen-section">
                <div id="myCarousel" className="carousel slide" data-ride="carousel">
                    <div className="carousel-inner" role="listbox">
                        <div className="item">
                            <div className="row">
                                <div className="col-md-6 col-sm-6 col-xs-6">
                                    <img src="/media/files/15/142/5945746612d55.PNG" alt="screen1" title="screen1" width="500px" />
                                </div>
                                <div className="col-md-6 col-sm-6 col-xs-6">
                                    <img src="/media/files/15/143/5945746627f90.PNG" alt="screen2" title="screen2" width="500px" />
                                </div>
                            </div>
                        </div>
                        <div className="item active">
                            <div className="row">
                                <div className="col-md-6 col-sm-6 col-xs-6">
                                    <img src="/media/files/15/138/5945746638e1e.PNG" alt="screen1" title="screen1" width="500px" />
                                </div>
                                <div className="col-md-6 col-sm-6 col-xs-6">
                                    <img src="/media/files/15/139/594574664569c.PNG" alt="screen2" title="screen2" width="500px" />
                                </div>
                            </div>
                        </div>
                        <div className="item">
                            <div className="row">
                                <div className="col-md-6 col-sm-6 col-xs-6">
                                    <img src="/media/files/15/140/5945746652124.PNG" alt="screen1" title="screen1" width="500px" />
                                </div>
                                <div className="col-md-6 col-sm-6 col-xs-6">
                                    <img src="/media/files/15/141/594574666304f.PNG" alt="screen2" title="screen2" width="500px" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <a className="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
                        <span className="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                        <span className="sr-only">Previous</span>
                    </a>
                    <a className="right carousel-control" href="#myCarousel" role="button" data-slide="next">
                        <span className="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                        <span className="sr-only">Next</span>
                    </a>
                </div>
            </div>
        </>
    );
}

export default Step1;
