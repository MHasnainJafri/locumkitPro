import { Tooltip as ReactTooltip } from "react-tooltip";
import ReCAPTCHA from "react-google-recaptcha";
import TextInput from "react-autocomplete-input";
import { useEffect, useRef, useState } from "react";
import axios from "axios";

function Step2({ user, setUser, setStep, errors, setErrors }) {
    const login_controller = useRef();
    const email_controller = useRef();

    useEffect(() => {
        setUser({ ...user, g_recaptcha_response: null });
        if (window.grecaptcha) {
            window.grecaptcha.reset();
        }
    }, []);

    const validateFields = () => {
        if (
            user.firstname &&
            user.firstname.length >= 2 &&
            user.lastname &&
            user.lastname.length >= 2 &&
            user.email &&
            /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(user.email) &&
            user.is_email_valid &&
            !errors.email &&
            user.username &&
            user.username.length >= 6 &&
            user.username.length <= 20 &&
            user.is_username_valid &&
            !errors.username &&
            user.password &&
            user.password_confirmation &&
            user.password.length >= 6 &&
            user.password == user.password_confirmation &&
            (user.role == 3 ? user.store && user.store.length >= 5 : true) &&
            user.address &&
            user.address.length >= 10 &&
            user.city &&
            user.city.length >= 2 &&
            user.zip &&
            user.zip.length >= 5 &&
            user.zip.length <= 7 &&
            (user.role == 3 ? user.telephone && user.telephone.length == 11 : true) &&
            (user.role == 2 ? user.mobile && user.mobile.length == 11 : true)
            //&& user.g_recaptcha_response
        ) {
            setErrors({});
            setStep(3);
            return;
        }
        let newErrors = { ...errors };
        if (!user.firstname || user.firstname?.length < 2) {
            newErrors.firstname = "Please enter firstname of min 2 characters";
        }
        if (!user.lastname || user.lastname?.length < 2) {
            newErrors.lastname = "Please enter lastname of min 2 characters";
        }
        if (!user.email || /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(user.email) == false) {
            newErrors.email = "Email is invalid. Please type a valid email first!";
        }
        if (!user.is_email_valid) {
            newErrors.email = "Email is already taken. Please choose some other email first!";
        }
        if (!user.username || user.username.length < 6 || user.username.length > 20) {
            newErrors.username = "Enter a username of 6-20 characters long.";
        }
        if (!user.is_username_valid) {
            newErrors.username = "Username is already taken. Please choose some other username first!";
        }
        if (!user.password || user.password?.length < 6) {
            newErrors.password = "Please enter password of min 6 characters";
        }
        if (!user.password_confirmation || user.password_confirmation != user.password) {
            newErrors.password_confirmation = "Password does not match.";
        }
        if (user.role == 3 && (!user.store || user.store.length < 5)) {
            newErrors.store = "Please enter store name of min 5 characters";
        }
        if (!user.address || user.address?.length < 10) {
            newErrors.address = "Please enter address of min 10 characters";
        }
        if (!user.city || user.city?.length < 2) {
            newErrors.city = "Please enter city of min 2 characters";
        }
        if (!user.zip || user.zip?.length < 5 || user.zip?.length > 7) {
            newErrors.zip = "Please enter valid postcode of length 5-7 characters";
        }
        if (user.role == 3 && (!user.telephone || user.telephone.length != 11)) {
            newErrors.telephone = "Please enter telephone of 11 digits";
        }
        if (user.role == 2 && (!user.mobile || user.mobile?.length != 11)) {
            newErrors.mobile = "Please enter mobile number of 11 digits";
        }
        if (!user.g_recaptcha_response) {
            newErrors.g_recaptcha_response = "Please fill captcha";
        }

        setErrors(newErrors);
    };

    const handleEmailValidation = (email) => {
        const newUser = { ...user, email: email };
        setUser(newUser);

        if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email)) {
            if (email_controller.current) {
                email_controller.current.abort();
            }
            email_controller.current = new AbortController();
            axios
                .post(
                    REGISTRATION_VALIDATION_URI,
                    {
                        check_type: "user_email",
                        email: email,
                    },
                    {
                        headers: {
                            "X-CSRF-TOKEN": CSRF_TOKEN,
                        },
                        signal: email_controller.current.signal,
                    }
                )
                .then((res) => {
                    if (res.data.email_exists == true) {
                        setErrors({ ...errors, email: "Email already exists." });
                        setUser({ ...newUser, is_email_valid: false });
                    } else {
                        setErrors({ ...errors, email: "" });
                        setUser({ ...newUser, is_email_valid: true });
                    }
                })
                .catch((err) => {});
        } else {
            setErrors({ ...errors, email: "You have entered an invalid email address!" });
        }
    };

    const handleUsernameValidation = (username) => {
        const newUser = { ...user, username: username };
        setUser(newUser);
        if (username.length < 6) {
            setErrors({ ...errors, username: "Please enter username with a minimum 6 of characters." });
            return;
        }
        if (username.length > 20) {
            setErrors({ ...errors, username: "Please enter username with a maximum of 20 characters." });
            return;
        }
        if (login_controller.current) {
            login_controller.current.abort();
        }
        login_controller.current = new AbortController();
        axios
            .post(
                REGISTRATION_VALIDATION_URI,
                {
                    check_type: "login_check",
                    login: username,
                },
                {
                    headers: {
                        "X-CSRF-TOKEN": CSRF_TOKEN,
                    },
                    signal: login_controller.current.signal,
                }
            )
            .then((res) => {
                if (res.data.login_exists == true) {
                    setErrors({ ...errors, username: "Username already exists." });
                    setUser({ ...newUser, is_username_valid: false });
                } else {
                    setErrors({ ...errors, username: "" });
                    setUser({ ...newUser, is_username_valid: true });
                }
            })
            .catch((err) => {});
    };

    const onGoogleRecaptchaChange = (value) => {
        setUser({ ...user, g_recaptcha_response: value });
    };

    const handleConfirmPasswordValidation = (password_confirmation) => {
        setUser({ ...user, password_confirmation: password_confirmation });
        if (user.password != password_confirmation) {
            setErrors({ ...errors, password_confirmation: "Password does not match." });
        } else {
            setErrors({ ...errors, password_confirmation: "" });
        }
    };

    return (
        <div id="step2" className="details-style step2-personal-info">
            <div className="col-md-12">
                <h2 className="reg-step-title text-center">
                    <span>Personal information</span>
                </h2>
            </div>
            <div className="col-md-12 pad0 form-group">
                <div className="col-md-6 col-sm-6">
                    <label htmlFor="email">
                        First name<i className="fa fa-asterisk required-stars required-stars" aria-hidden="true"></i>
                    </label>
                    <input
                        className="form-control input-text width-100"
                        type="text"
                        name="fname"
                        id="fname"
                        minLength={2}
                        maxLength={20}
                        autoComplete="off"
                        onChange={(e) => setUser({ ...user, firstname: e.target.value.trim() })}
                        value={user.firstname ?? ""}
                    />
                    {errors && errors.firstname && (
                        <div className="css_error" id="fname_error">
                            {errors.firstname}
                        </div>
                    )}
                </div>
                <div className="col-md-6 col-sm-6">
                    <label htmlFor="lname">
                        Last name<i className="fa fa-asterisk required-stars required-stars" aria-hidden="true"></i>
                    </label>
                    <input
                        className="form-control input-text width-100"
                        type="text"
                        name="lname"
                        id="lname"
                        minLength={2}
                        maxLength={20}
                        autoComplete="off"
                        onChange={(e) => setUser({ ...user, lastname: e.target.value.trim() })}
                        value={user.lastname ?? ""}
                    />
                    {errors && errors.lastname && (
                        <div className="css_error" id="lname_error">
                            {errors.lastname}
                        </div>
                    )}
                </div>
            </div>
            <div className="col-md-12 pad0 form-group">
                <div className="col-md-6 col-sm-6">
                    <label htmlFor="email">
                        Your email<i className="fa fa-asterisk required-stars required-stars" aria-hidden="true"></i>
                    </label>
                    <input className="form-control input-text width-100" type="email" onChange={(e) => handleEmailValidation(e.target.value.trim())} name="email" id="email" autoComplete="off" value={user.email ?? ""} />
                    {errors && errors.email && (
                        <div className="css_error" id="email_error">
                            {errors.email}
                        </div>
                    )}
                </div>
                <div className="col-md-6 col-sm-6">
                    <label htmlFor="login">
                        Username<i className="fa fa-asterisk required-stars required-stars" aria-hidden="true"></i>
                    </label>
                    <input
                        className="form-control input-text width-100"
                        type="text"
                        onChange={(e) => handleUsernameValidation(e.target.value.trim())}
                        name="login"
                        id="login"
                        autoComplete="off"
                        minLength={6}
                        maxLength={20}
                        value={user.username ?? ""}
                    />
                    {errors && errors.username && (
                        <div className="css_error" id="login_error">
                            {errors.username}
                        </div>
                    )}
                </div>
            </div>
            <div className="col-md-12 pad0 form-group">
                <div className="col-md-6 col-sm-6">
                    <label htmlFor="login">
                        Password<i className="fa fa-asterisk required-stars required-stars" aria-hidden="true"></i>
                    </label>
                    <input className="form-control input-text width-100" type="password" name="password" id="upassword" onChange={(e) => setUser({ ...user, password: e.target.value.trim() })} value={user.password ?? ""} />
                    {errors && errors.password && (
                        <div className="css_error" id="upassword_error">
                            {errors.password}
                        </div>
                    )}
                </div>
                <div className="col-md-6 col-sm-6">
                    <label htmlFor="login">
                        Confirm password<i className="fa fa-asterisk required-stars required-stars" aria-hidden="true"></i>
                    </label>
                    <input
                        className="form-control input-text width-100"
                        type="password"
                        name="password_confirmation"
                        id="cpassword"
                        onChange={(e) => handleConfirmPasswordValidation(e.target.value.trim())}
                        value={user.password_confirmation ?? ""}
                    />
                    {errors && errors.password_confirmation && (
                        <div className="css_error" id="cpassword_error">
                            {errors.password_confirmation}
                        </div>
                    )}
                </div>
            </div>
            <div className="col-md-12 pad0 form-group">
                {user.role == 2 ? (
                    <div className="col-md-6 col-sm-6">
                        <label htmlFor="company" id="company_txt">
                            Company name
                            <p
                                id="my-company-element"
                                style={{ display: "inline", paddingInlineStart: "10px", cursor: "pointer" }}
                                data-tooltip-content="Question only applicable if you work through a registered company. Not applicable if you are working as a self employed freelancer."
                            >
                                <i className="fa fa-question-circle" aria-hidden="true"></i>
                            </p>
                        </label>
                        <input
                            className="form-control input-text width-100"
                            type="text"
                            name="company"
                            id="company"
                            autoComplete="off"
                            onChange={(e) => setUser({ ...user, company: e.target.value })}
                            value={user.company ?? ""}
                            maxLength={20}
                        />
                    </div>
                ) : (
                    <div className="col-md-6 col-sm-6">
                        <label htmlFor="conpany" id="company_txt">
                            Store Name <i className="fa fa-asterisk required-stars required-stars" aria-hidden="true"></i>
                            <p
                                id="my-store-element"
                                style={{ display: "inline", paddingInlineStart: "10px", cursor: "pointer" }}
                                data-tooltip-content="If you manage more than one store and would like to register multiple stores with Locumkit,you will have the option to do so later in the registration process. Please enter here, what you consider to be your main store from the Group."
                            >
                                <i className="fa fa-question-circle" aria-hidden="true"></i>
                            </p>
                        </label>
                        <input className="form-control input-text width-100" type="text" name="store" id="store" autoComplete="off" onChange={(e) => setUser({ ...user, store: e.target.value })} value={user.store ?? ""} maxLength={20} />
                        {errors && errors.store && (
                            <div className="css_error" id="store_error">
                                {errors.store}
                            </div>
                        )}
                    </div>
                )}

                <div className="col-md-6 col-sm-6">
                    <label htmlFor="address">
                        Address<i className="fa fa-asterisk required-stars required-stars" aria-hidden="true"></i>
                    </label>
                    <input
                        className="form-control input-text width-100"
                        type="text"
                        name="address"
                        id="address"
                        autoComplete="off"
                        onChange={(e) => setUser({ ...user, address: e.target.value })}
                        value={user.address ?? ""}
                        maxLength={200}
                        minLength={10}
                    />
                    {errors && errors.address && (
                        <div className="css_error" id="address_error">
                            {errors.address}
                        </div>
                    )}
                </div>
            </div>
            <div className="col-md-12 pad0 form-group">
                <div className="col-md-6 col-sm-6 city-selector">
                    <label htmlFor="login">
                        Town/City<i className="fa fa-asterisk required-stars required-stars" aria-hidden="true"></i>
                    </label>
                    <TextInput
                        trigger={[""]}
                        className="form-control input-text width-100 ui-autocomplete-input"
                        Component="input"
                        options={AVAILABLE_TAGS}
                        onChange={(value) => setUser({ ...user, city: value })}
                        defaultValue={user.city}
                        value={user.city}
                        maxLength={30}
                    />
                    {errors && errors.city && (
                        <div className="css_error" id="city_error">
                            {errors.city}
                        </div>
                    )}
                </div>
                <div className="col-md-6 col-sm-6">
                    <label htmlFor="login">
                        Postcode<i className="fa fa-asterisk required-stars required-stars" aria-hidden="true"></i>
                    </label>
                    <input
                        className="form-control input-text width-100"
                        type="text"
                        name="zip"
                        id="zip"
                        autoComplete="off"
                        onChange={(e) => setUser({ ...user, zip: e.target.value })}
                        value={user.zip ?? ""}
                        minLength={5}
                        maxLength={7}
                    />
                    {errors && errors.zip && (
                        <div className="css_error" id="zip_error">
                            {errors.zip}
                        </div>
                    )}
                </div>
            </div>
            <div className="col-md-12 pad0 form-group">
                <div className="col-md-6 col-sm-6">
                    <label htmlFor="telephone" id="telephone_txt">
                        {user.role == 2 ? (
                            <>Home Telephone</>
                        ) : (
                            <>
                                Store Telephone<i className="fa fa-asterisk required-stars required-stars" aria-hidden="true"></i>
                            </>
                        )}
                    </label>
                    <input
                        className="form-control input-text width-100"
                        type="tel"
                        step={1}
                        name="telephone"
                        id="telephone"
                        autoComplete="off"
                        minLength={11}
                        maxLength={11}
                        onChange={(e) => setUser({ ...user, telephone: e.target.value.trim() })}
                        value={user.telephone ?? ""}
                    />
                    {errors && errors.telephone && (
                        <div className="css_error" id="telephone_error">
                            {errors.telephone}
                        </div>
                    )}
                </div>
                <div className="col-md-6 col-sm-6">
                    <label htmlFor="mobile" id="mobile_number">
                        {user.role == 2 ? (
                            <>
                                Mobile number<i className="fa fa-asterisk required-stars required-stars" aria-hidden="true"></i>
                            </>
                        ) : (
                            <>Mobile number(optional)</>
                        )}
                    </label>
                    <input
                        className="form-control input-text width-100"
                        type="tel"
                        step={1}
                        name="mobile"
                        id="mobile"
                        minLength={11}
                        maxLength={11}
                        onChange={(e) => setUser({ ...user, mobile: e.target.value.trim() })}
                        value={user.mobile ?? ""}
                    />
                    {errors && errors.mobile && (
                        <div className="css_error" id="mobile_error">
                            {errors.mobile}
                        </div>
                    )}
                </div>
            </div>
            <div className="col-md-12 pad0 form-group text-center" style={{ marginTop: "20px", display: "flex", justifyContent: "center", alignItems: "center", flexDirection: "column" }}>
                <ReCAPTCHA sitekey={GOOGLE_RECAPTCHA_SITE_KEY} onChange={onGoogleRecaptchaChange} />
                {errors && errors.g_recaptcha_response && (
                    <span className="css_error" id="g_recaptcha_response_error">
                        {errors.g_recaptcha_response}
                    </span>
                )}
            </div>
            <div className="col-md-12 pad0 form-group text-center" style={{ marginTop: "20px" }}>
                <span className="formlft">
                    <button type="button" className="btn btn-default btn-1 lkbtn" onClick={(e) => setStep(1)}>
                        <span>
                            <i className="fa fa-angle-double-left" aria-hidden="true"></i> &nbsp;&nbsp;Back
                        </span>
                    </button>
                </span>
                <span className="formlft">
                    <button type="button" className="btn btn-default btn-1 lkbtn" id="qus_next" onClick={validateFields}>
                        <span>
                            Next &nbsp;&nbsp;<i className="fa fa-angle-double-right" aria-hidden="true"></i>
                        </span>
                    </button>
                </span>
            </div>

            <ReactTooltip style={{ maxWidth: "300px" }} anchorId="my-company-element" />
            <ReactTooltip style={{ maxWidth: "300px" }} anchorId="my-store-element" />
        </div>
    );
}

export default Step2;
