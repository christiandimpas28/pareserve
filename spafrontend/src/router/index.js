import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from '../stores/auth';
import {RouterView} from "vue-router";

/* Views */
import Home from '../views/Home.vue';
import Customer from '../views/Customer.vue';
import Admin from '../views/Admin.vue';
import Partner from '../views/Merchant.vue';
import PageNotFound from '../views/PageNotFound.vue';

/* Common Components */
import Register from '../components/Register.vue';
import Login from '../components/Login.vue';
import ForgotPassword from '../components/ForgotPassword.vue';
import ResetPassword from '../components/ResetPassword.vue';

/* Sub Components */
import MyAccount from '../components/customer/MyAccount.vue';
import Profile from '../components/customer/Profile.vue';
import Bookings from '../components/customer/Bookings.vue'; //Customer Confirmed Booking Transactions
import Otp from '../components/home/Otp.vue';

import ListingSetup from '../components/home/ListingSetup.vue';
import Listings from '../components/home/Listings.vue';
import ViewProductDetails from '../components/home/ProductDetails.vue';
import Books from '../components/home/Books.vue'; //List of unconfirmed bookings or Booking Cart
import BookPayment from '../components/home/BookPayment.vue';
import ListingSearch from '../components/home/ListingSearch.vue';


/* partner|merchant */
import MerchantDashboard from '../components/merchant/Dashboard.vue';
import BusinessProfile from '../components/merchant/Profile.vue';
import MerchantTransactions from '../components/merchant/Transactions.vue';
import MerchantListings from '../components/merchant/Listings.vue';
import MerchantProducts from '../components/merchant/Products.vue';

import ViewProduct from '../components/merchant/ProductView.vue';
import CreatetProduct from '../components/merchant/CreateProduct.vue';
import ManageProduct from '../components/merchant/ManageProduct.vue';

/* Payments */
import PaymentSuccess from '../components/home/PaymentSuccess.vue';
import PaymentFailed from '../components/home/PaymentFailed.vue';
import PaymentCancelled from '../components/home/PaymentCancelled.vue';

/* Error Page */
import Error401 from '../views/Error401.vue';
import Error403 from '../views/Error403.vue';

const history = createWebHistory();
const routes = [
    { 
        path: '/', 
        name: 'Home',
        component: Home,
        // component: RouterView,
        children: [
            {
                name: 'ListingSetup',
                path: 'partner/signup',
                component: ListingSetup,
                meta: { auth: false }
            }, {
                name: 'AccountOtp',
                path: 'account/otp',
                component: Otp,
                meta: { auth: true }
            }, {
                name: 'Listings',
                path: 'listings',
                component: Listings,
                meta: { auth: false }
            }, {
                name: 'Booking',
                path: 'booking/confirm-and-pay/:id',
                component: Books,
                meta: { auth: true }
            }, {
                name: 'BookPayment',
                path: 'book/payment/response',
                component: BookPayment,
                meta: { auth: true }
            }, {
                name: 'ViewProductDetails',
                path: 'product/view/:id',
                component: ViewProductDetails,
                meta: { auth: false }
            }, {
                name: 'ListingsSearch',
                path: 'listings/search',
                component: ListingSearch,
                meta: { auth: false }
            }, {
                name: 'PaymentSuccess',
                path: 'booking/payment/success/:id/:booking_ref_no',
                component: PaymentSuccess,
                meta: { auth: true }
            }, {
                name: 'PaymentFailed',
                path: 'booking/payment/failed/:id/:booking_ref_no',
                component: PaymentFailed,
                meta: { auth: true }
            }, {
                name: 'PaymentCancelled',
                path: 'booking/payment/cancelled/:id/:booking_ref_no',
                component: PaymentCancelled,
                meta: { auth: true }
            }

        ]
    }, { 
        path: '/customer', 
        name: 'Customer',
        component: Customer,
        meta: { auth: true, utype: 'Customer', title: 'Dashboard' },
        children: [
            {
                name: 'CustomerAccount',
                path: 'account',
                component: MyAccount,
                meta: { auth: true, utype: 'Customer', title: 'My Account' }
            }, {
                name: 'CustomerProfile',
                path: 'profile',
                component: Profile,
                meta: { auth: true, utype: 'Customer', title: 'My Profile' }
            }, {
                name: 'CustomerBookings',
                path: 'bookings',
                component: Bookings,
                meta: { auth: true, utype: 'Customer', title: 'Booking Transactions' }
            }
        ]
    },{ 
        path: '/admin/dashboard', 
        name: 'Admin',
        component: Admin,
        meta: { auth: true, utype: 'Admin' }
    }, { 
        path: '/partner', 
        name: 'Partner',
        component: Partner,
        meta: { auth: true, utype: 'Partner', title: 'Dashboard' },
        children: [
            {
                name: 'MerchantDashboard',
                path: 'dashboard',
                component: MerchantDashboard,
                meta: { auth: true, utype: 'Partner', title: 'Dashboard' }
            },
            {
                name: 'BusinessProfile',
                path: 'profile',
                component: BusinessProfile,
                meta: { auth: true, utype: 'Partner', title: 'Merchant Profile' }
            },
            {
                name: 'MerchantTransactions',
                path: 'transactions',
                component: MerchantTransactions,
                meta: { auth: true, utype: 'Partner', title: 'Transactions' }
            },
            {
                name: 'MerchantListings',
                path: 'listings',
                component: MerchantListings,
                meta: { auth: true, utype: 'Partner', title: 'Listings' }
            },
            {
                name: 'MerchantProducts',
                path: 'products',
                component: MerchantProducts,
                meta: { auth: true, utype: 'Partner', title: 'My Products' }
            },
            {
                name: 'AddListing',
                path: 'listing/create/:merchantId',
                component: MerchantListings,
                meta: { auth: true, utype: 'Partner', title: 'Add New Listing' }
            },
            {
                name: 'EditListing',
                path: 'listing/edit/:merchantId/property/:id',
                component: MerchantListings,
                meta: { auth: true, utype: 'Partner', title: 'Edit Listing' }
            },
            {
                name: 'ViewPartnersProduct',
                path: 'product/details/:listingCategoryId/:id',
                component: ViewProduct,
                meta: { auth: true, utype: 'Partner', title: 'My Product' }
            },
            {
                name: 'CreateProduct',
                path: 'product/create/:listingCategoryId',
                component: CreatetProduct,
                meta: { auth: true, utype: 'Partner', title: 'Create Product' }
            },
            {
                name: 'ManageProduct',
                path: 'product/edit/:listingCategoryId/:id',
                component: ManageProduct,
                meta: { auth: true, utype: 'Partner', title: 'Manage Product' }
            }
        ],
        
    }, { 
        path: '/login', 
        name: 'Login',
        component: Login ,
        meta: { auth: false }
    }, { 
        path: '/register', 
        name: 'Register',
        component: Register,
        meta: { auth: false }
    }, {
        path: '/forgot-password',
        name: 'ForgotPassword',
        component: ForgotPassword,
        meta: { auth: false }
    }, {
        path: '/password-reset/:token',
        name: 'ResetPassword',
        component: ResetPassword,
        meta: { auth: false }
    }, {
        path: '/:pathMatch(.*)*',
        name: 'PageNotFound',
        component: PageNotFound,
    }, {
        path: '/unauthorized',
        name: 'Error401',
        component: Error401
    }, {
        path: '/forbidden',
        name: 'Error403',
        component: Error403
    }
    
]
/*
const routes = [
    { path: "/", name: "Home", component: Home, meta:{ requiresAuth: false }, }, 
    { path: "/admin/dashboard", name: "Admin", component: Admin, meta:{ requiresAuth: true, ut: 'Admin' } }, 
    { path: "/partner/dashboard", name: "Partner", component: Partner, meta:{ requiresAuth: true, ut: 'Partner', verified: true } }, 
    { path: "/login", name: "Login", component: ()=> import("../components/f/Login.vue"), meta: { requiresAuth: false, }, }, 
    { path: "/register", name: "Register", component: ()=> import("../components/f/Register.vue"), meta: { requiresAuth: false, }, }, 
    { 
        path: "/otp", 
        name: "VerifyOtp", 
        component: ()=> import("../components/f/Otp.vue"),
        meta:{ requiresAuth: true },
        // beforeEnter: (to, from, next) => {
        //     console.log("OI! OTP Page ni", to,name);
        //     if (!to.meta.guest) {
        //         // Validate and proceed
        //         console.log("to.meta.guest", to.meta.guest)
        //     } 
        //     return next();
        // }
    }, 
    {   path: "/myaccount", 
        name: "MyAccount", 
        component: ()=> import("../components/f/MyAccount.vue"), 
        meta: { requiresAuth: true, ut: 'Customer', verified: true }, 
        // beforeEnter: (to, from, next) => {
        //     console.log("MyAccount BEFORE ENTER:", authStore);
        // }
    }, 
    { path: "/listing-setup", name: "ListingSetup", component: ()=> import("../components/f/ListingSetup.vue"), meta: { requiresAuth: false, }, }, 
];
*/

const router = createRouter({
    history,
    routes,
});


// console.log('INDEX - Router');

// router.beforeResolve((to, from) => {
//     console.log('User Authenticated');
//     console.log('Coming from', from.path);
//     console.log('Going to', to.path);
// });


// router.beforeEach((to, from, next) => {
//     // const authStore = useAuthStore();

//     // (async() => {
//     //     console.log('1');
//     //     await authStore.getUser('Route BeforeEach');
//     //     const isUserAuth = authStore.user == null? false: true;
//     //     console.log("isUserAuth", isUserAuth);
//     //     if ('auth' in to.meta && to.meta.auth && !isUserAuth) {
//     //         next('/login')
//     //     } else if ('auth' in to.meta && !to.meta.auth && isUserAuth && to.name == 'Login') {
//     //         next('/kaka');
//     //     } else if ('auth' in to.meta && !to.meta.auth && isUserAuth && to.name == 'Register') {
//     //         next('/okok');
//     //     } else if ('auth' in to.meta && !to.meta.auth && isUserAuth && to.name == 'ListingSetup') {
//     //         next('/');
//     //     }else if ('auth' in to.meta && to.meta.auth && isUserAuth && 'utype' in to.meta && to.meta.utype != authStore.user.user_type) {
//     //         next('/Error'); //Authorize Error
//     //     } else {
//     //         next();
//     //     }
//     //     console.log('2');
//     // })()
//     console.log('beforeEach');
// });


// router.beforeEach(async (to) => {
//     const authStore = useAuthStore();
//     const requiresAuth = to.matched.some((record) => record.meta.auth);
//     console.log("beforeEach", requiresAuth, "USER", authStore.getUser('Route BeforeEach'), "TO" , to.path);
//     if (!requiresAuth && to.name == 'Login' && (authStore.getUser('Route BeforeEach') != null) ) {
//         return '/';
//     } 
//     // if (!requiresAuth && to.name == "Register" && (authStore.getUser('Route BeforeEach') != null) ) {
//     //     return '/'
//     // }

//     // if (!requiresAuth && to.name == "ListingSetup" && (authStore.getUser('Route BeforeEach') != null) ) {
//     //     return '/'
//     // }
// });

// router.beforeResolve( async (to, from) => {
//     const authStore = useAuthStore();
    
//     console.log('User Authenticated', authStore.user);
//     console.log('Coming from:', from.path)
//     console.log('Going to:', to.path)
// })

export default router;


