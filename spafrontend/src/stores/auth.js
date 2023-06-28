import { defineStore } from "pinia";
import axios from "axios";

export const useAuthStore = defineStore("auth", {
    state: () => ({
        authUser: null,
        authUserOtp: null,
        authErrors: [],
        authStatus: null,
    }),
    getters: {
        user: (state) => state.authUser,
        otp: (state) => state.authUserOtp,
        errors: (state) => state.authErrors,
        status: (state) => state.authStatus,
    },
    actions: {
        async getToken (){
            await axios.get("/sanctum/csrf-cookie");
        },
        async getUser(fr) {
            await this.getToken();
            try {
                const data = await axios.get("/api/user").catch((err) => {
                    console.log("getUser Catch", err);
                    return Promise.reject(err);
                 });
                
                this.authUser = data.data;

                // const data = await axios.get("/api/user")
                //     .then(function (response) {
                //         // handle success
                //         console.log("handle success", response.data);
                //         this.authUser = response.data;
                //     })
                //     .catch( function (error) {
                //         if (error.response) {
                //             console.log("getUser Error", error.response);
                //         }
                //     });
                
            } catch (error) {
                console.log("getUser Err", error);   
            }
        },
        async otpInit() {
            await this.getToken();
            const data = await axios.get("/otp/send/init");
            this.authUserOtp = data.data;
            if (this.authUserOtp && this.authUserOtp.redirect)
                this.router.push(this.authUserOtp.redirect);
        },
        async otpVerify(data) {
            console.log("OTP.VERIFY");
            await this.getToken();
            const response = await axios.post('/otp/send/verify', {
                otp: data.otp,
            });
            this.authUserOtp = response.data;
            // if (this.authUserOtp && this.authUserOtp.redirect)
            //     this.router.push(this.authUserOtp.redirect);

            if (this.authUserOtp) {
                if (localStorage.getItem('guestCheckoutAttempt')===null){
                    this.router.push(this.authUserOtp.redirect);
                } else {
                    const gca = JSON.parse(localStorage.getItem('guestCheckoutAttempt'));
                    this.router.push(gca.url);
                }
            }

                
            
        },
        async otpRequest(e) {
            console.log(e);
            e.target.disabled = true;
            e.target.innerText = "Sending Request...";
            await this.getToken();
            const data = await axios.get("/otp/send/request");
            this.authUserOtp = data.data;
            console.log("REQ OTP:", this.authUserOtp);
        },
        async handleLogin (data) {
            this.authErrors = [];
            this.authStatus = null;
            await this.getToken();
            try {
                await axios.post('/login', {
                    email: data.email,
                    password: data.password
                });
                
                await this.getUser('fn Handle Login');
                const is_verified = await this.verifyUser();
                if (!is_verified) {
                    this.router.push("/account/otp");
                    return;
                }

                if (localStorage.getItem('guestCheckoutAttempt')===null){
                    this.authRedirect();
                } else {
                    const gca = JSON.parse(localStorage.getItem('guestCheckoutAttempt'));
                    this.router.push(gca.url);
                }
                
            } catch (error) {
                if (error.response.status === 422){
                    console.log("Validation", error);
                    this.authErrors = error.response.data.errors;
                }
            }
            

            
        },
        async handleRegister (data) {
            this.authErrors = [];
            await this.getToken();
            try {
                await axios.post('/register?ut=customer', {
                    name: data.name,
                    email: data.email,
                    password: data.password,
                    password_confirmation: data.password_confirmation,
                    user_type: data.user_type,
                });
                await this.getUser('fn Handle Register');
                const is_verified = await this.verifyUser();
                if (!is_verified) {
                    this.router.push("/account/otp");
                    return;
                }
                this.authRedirect();
            } catch (error) {
                if (error.response.status === 422){
                    console.log("Validation", error);
                    this.authErrors = error.response.data.errors;
                }
            }
        },
        async handleRegisterPartner (data) {
            this.authErrors = [];
            await this.getToken();

            try {
                await axios.post('/register?ut=partner', {
                    name: data.name,
                    email: data.email,
                    password: data.password,
                    password_confirmation: data.password_confirmation,
                    user_type: data.user_type,
                });
                await this.getUser('fn Handle Reg Partner');
                const is_verified = await this.verifyUser();
                if (!is_verified) {
                    this.router.push("/account/otp");
                    return;
                }
                this.authRedirect();
            } catch (error) {
                if (error.response.status === 422){
                    console.log("Validation", error);
                    this.authErrors = error.response.data.errors;
                }
            }
            
        },
        async authRedirect() {
            if (this.authUser==null){
                console.log("this.authUser initially null");
                await this.getUser('fn Auto Redirect');
            }
            
            if (this.authUser.user_type == 'Customer') {
                this.router.push({name: 'Home'});
            }

            if (this.authUser.user_type == 'Partner') {
                this.router.push({name: 'Partner'});
            }

            if (this.authUser.user_type == 'Admin') {
                this.router.push("/admin/dashboard");
            }
        },
        async handleLogout() {
            await axios.post('/logout');
            this.authUser=null;
            this.router.push("/listings");
            console.log('handleLogout 2');
        },
        async handleForgotPassword(email) {
            this.authErrors = [];
            await this.getToken();
            try {
                const response = await axios.post('/forgot-password', {
                    email: email,
                });
                this.authStatus = response.data.status;
            } catch (error) {
                if (error.response.status === 422){
                    this.authErrors = error.response.data.errors;
                }
            }
        },
        async handleResetPassword(resetData) {
            this.authErrors = [];
            await this.getToken();
            try {
                const response = await axios.post('/reset-password', resetData);
                this.authStatus = response.data.status;
                this.router.push({name: 'Login'});
            } catch (error) {
                if (error.response.status === 422){
                    this.authErrors = error.response.data.errors;
                }
            }
        },
        async verifyUser(){
            return this.authUser && this.authUser.email_verified_at !=null;
        },
        async testMechant() {
            await this.getToken();
            const data = await axios.get("/merchants");
        }
    }
})