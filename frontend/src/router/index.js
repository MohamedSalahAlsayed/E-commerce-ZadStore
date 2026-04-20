import { createRouter, createWebHashHistory } from "vue-router";
import HomePage from "../views/User/HomePage.vue";
import AboutPage from "../views/AboutPage.vue";
import Product from "../views/User/ProductPage.vue";
import ProductDetails from "../views/User/ProductDetails.vue";
import FavourateProduct from "@/views/User/FavourateProduct.vue";
import BrandPage from "../views/User/BrandPage.vue";
import CartPage from "@/views/User/CartPage.vue";
import ProductsPage from "@/views/User/ProductsPage.vue";
// import ProductPage from "../views/User/ProductPage.vue";
import AddOrder from "@/views/User/AddOrder.vue";
import StatisticsPage from "@/views/User/StatisticsPage.vue";
import OrdersPage from "@/views/User/OrdersPage.vue";
import CommissionTransferRequests from "@/views/User/CommissionTransferRequests.vue";
import ElhesapatePage from "@/views/User/ElhesapatePage.vue";
import AddModeratorPage from "@/views/User/AddModeratorPage.vue";
import HomeGestPage from "@/views/Gest/HomeGestPage.vue";
import OrderSuccess from "@/views/User/OrderSuccess.vue";
import LogIn from "@/views/Auth/LogIn.vue";
import RegisterNow from "@/views/Auth/RegisterNow.vue";
import GoogleCallback from "@/views/Auth/GoogleCallback.vue";
import LogOut from "@/views/Auth/LogOut.vue";

// Guest / CMS Pages
import BlogPage from "@/views/CMS/BlogPage.vue";
import ContactPage from "@/views/CMS/ContactPage.vue";
import ProfilePage from "@/views/User/UserProfile/ProfilePage.vue";
import EditProfilePage from "@/views/User/UserProfile/EditProfilePage.vue";
import ForgetPassword from "@/views/Auth/ForgetPassword.vue";
import MessagePage from "@/views/User/UserProfile/MessagePage.vue";
import NotificationPage from "@/views/User/UserProfile/NotificationPage.vue";
import TagerPage from "@/views/User/TagerPage.vue";
import AdminDashboard from "@/views/Dashboard/AdminDashboard.vue";
import MangProductPage from "@/views/Dashboard/MangProductPage.vue";
import MangOrder from "@/views/Dashboard/MangOrder.vue";
import MangCategoury from "@/views/Dashboard/MangCategoury.vue";
import MangUsers from "@/views/Dashboard/MangUsers.vue";
import InventoryManager from "@/views/Dashboard/InventoryManager.vue";
import CouponsDiscounts from "@/views/Dashboard/CouponsDiscounts.vue";
import MangBanner from "@/views/Dashboard/MangBanner.vue";
import RatingsReviews from "@/views/Dashboard/RatingsReviews.vue";
import ShippingZone from "@/views/Dashboard/ShippingZone.vue";
import StoreSetting from "@/views/Dashboard/StoreSetting.vue";
import ContactMessage from "@/views/Dashboard/ContactMessage.vue";
import MangReturns from "@/views/Dashboard/MangReturns.vue";
import AddTager from "@/views/Dashboard/AddTager.vue";
import MangPurchases from "@/views/Dashboard/MangPurchases.vue";
import StaffManagement from "@/views/Dashboard/StaffManagement.vue";
import MangBlog from "@/views/Dashboard/MangBlog.vue";
import { useAuthStore } from "@/store/auth/LogIn";

const routes = [
  // مسارات الزوار (Guest Landing Ecosystem)
  {
    path: "/",
    name: "HomeGestPage",
    component: HomeGestPage,
    meta: {
      layout: "GuestLayout",
      guestOnly: true,
      title: "الرئيسية",
      description:
        "مرحباً بك في ZadStore، أفضل منصة للتجارة الإلكترونية والتسويق بالعمولة.",
    },
  },
  {
    path: "/BlogPage",
    name: "BlogPage",
    component: BlogPage,
    meta: {
      layout: "GuestLayout",
      title: "المدونة",
      description:
        "اطلع على آخر الأخبار والمقالات التعليمية حول التجارة الإلكترونية.",
    },
  },
  {
    path: "/ContactPage",
    name: "ContactPage",
    component: ContactPage,
    meta: {
      layout: "GuestLayout",
      title: "اتصل بنا",
      description: "نحن هنا لمساعدتك. تواصل معنا لأي استفسارات أو دعم فني.",
    },
  },

  // مسار الموقع الرئيسي للمستخدمين
  {
    path: "/home",
    name: "home",
    component: HomePage,
    meta: {
      requiresAuth: true,
      title: "لوحة التحكم",
      description: "أهلاً بك في لوحة تحكم ZadStore الخاصة بك.",
    },
  },
  {
    path: "/AboutPage",
    name: "about",
    component: AboutPage,
    meta: { layout: "GuestLayout" },
  },
  {
    path: "/User/ProductPage",
    name: "Product",
    component: Product,
    meta: { requiresAuth: true },
  },
  {
    path: "/User/TagerPage",
    name: "TagerPage",
    component: TagerPage,
    meta: { requiresAuth: true },
  },
  {
    path: "/User/ProductDetails/:productId",
    name: "ProductDetail",
    component: ProductDetails,
    meta: { requiresAuth: true },
  },
  {
    path: "/User/FavourateProduct",
    name: "FavourateProduct",
    component: FavourateProduct,
    meta: { requiresAuth: true },
  },
  {
    path: "/User/CartPage",
    name: "CartPage",
    component: CartPage,
    meta: { requiresAuth: true },
  },
  {
    path: "/User/category/:slug",
    name: "CategoryPage",
    component: () => import("../views/User/CategoryPage.vue"),
  },
  {
    path: "/User/brand/:slug",
    name: "BrandPage",
    component: BrandPage,
  },
  {
    path: "/search",
    name: "SearchPage",
    component: () => import("../views/User/SearchPage.vue"),
  },
  {
    path: "/User/ProductsPage",
    name: "ProductsPage",
    component: ProductsPage,
    meta: {
      requiresAuth: true,
      title: "المنتجات",
      description: "تصفح مجموعة واسعة من المنتجات المتميزة بأسعار تنافسية.",
    },
  },
  {
    path: "/User/AddOrder",
    name: "AddOrder",
    component: AddOrder,
    meta: { requiresAuth: true },
  },
  {
    path: "/User/OrderSuccess",
    name: "OrderSuccess",
    component: OrderSuccess,
    meta: { requiresAuth: true },
  },
  {
    path: "/User/ControlPannel/StatisticsPage",
    name: "StatisticsPage",
    component: StatisticsPage,
    meta: { requiresAuth: true },
  },
  {
    path: "/User/ControlPannel/OrdersPage",
    name: "OrdersPage",
    component: OrdersPage,
    meta: { requiresAuth: true },
  },
  {
    path: "/User/ControlPannel/CommissionTransferRequests",
    name: "CommissionTransferRequests",
    component: CommissionTransferRequests,
    meta: { requiresAuth: true },
  },
  {
    path: "/User/ControlPannel/ElhesapatePage",
    name: "ElhesapatePage",
    component: ElhesapatePage,
    meta: { requiresAuth: true },
  },
  {
    path: "/User/ControlPannel/AddModeratorPage",
    name: "AddModeratorPage",
    component: AddModeratorPage,
    meta: { requiresAuth: true },
  },
  {
    path: "/Auth/LogOut",
    name: "LogOut",
    component: LogOut,
    meta: { layout: "GuestLayout", requiresAuth: true },
  },
  {
    path: "/Auth/LogIn",
    name: "LogIn",
    component: LogIn,
    meta: {
      layout: "GuestLayout",
      guestOnly: true,
      title: "تسجيل الدخول",
      description: "سجل الدخول إلى حسابك في ZadStore لمتابعة أرباحك وطلباتك.",
    },
  },
  {
    path: "/Auth/RegisterNow",
    name: "RegisterNow",
    component: RegisterNow,
    meta: { layout: "GuestLayout", guestOnly: true },
  },
  {
    path: "/auth/callback",
    name: "GoogleCallback",
    component: GoogleCallback,
    meta: { layout: "GuestLayout", guestOnly: true },
  },
  {
    path: "/Auth/ForgetPassword",
    name: "ForgetPassword",
    component: ForgetPassword,
    meta: { layout: "GuestLayout" },
  },
  {
    path: "/User/UserProfile/ProfilePage",
    name: "ProfilePage",
    component: ProfilePage,
    meta: { requiresAuth: true },
  },
  {
    path: "/User/UserProfile/EditProfilePage",
    name: "EditProfilePage",
    component: EditProfilePage,
    meta: { requiresAuth: true },
  },
  {
    path: "/User/UserProfile/MessagePage",
    name: "MessagePage",
    component: MessagePage,
    meta: { requiresAuth: true },
  },
  {
    path: "/User/UserProfile/NotificationPage",
    name: "NotificationPage",
    component: NotificationPage,
    meta: { requiresAuth: true },
  },

  // ============================================
  // مسارات الداش بورد (تم إضافة الـ meta هنا)
  // ============================================
  {
    path: "/Dashboard/AdminDashboard",
    name: "AdminDashboard",
    component: AdminDashboard,
    meta: {
      layout: "DashboardLayout",
      requiresAuth: true,
      requiresAdmin: true,
    },
  },
  {
    path: "/Dashboard/MangProductPage",
    name: "MangProductPage",
    component: MangProductPage,
    meta: {
      layout: "DashboardLayout",
      requiresAuth: true,
      requiresAdmin: true,
    },
  },
  {
    path: "/Dashboard/MangOrder",
    name: "MangOrder",
    component: MangOrder,
    meta: {
      layout: "DashboardLayout",
      requiresAuth: true,
      requiresAdmin: true,
    },
  },
  {
    path: "/Dashboard/MangCategoury",
    name: "MangCategoury",
    component: MangCategoury,
    meta: {
      layout: "DashboardLayout",
      requiresAuth: true,
      requiresAdmin: true,
    },
  },
  {
    path: "/Dashboard/MangUsers",
    name: "MangUsers",
    component: MangUsers,
    meta: {
      layout: "DashboardLayout",
      requiresAuth: true,
      requiresAdmin: true,
    },
  },
  {
    path: "/Dashboard/InventoryManager",
    name: "InventoryManager",
    component: InventoryManager,
    meta: {
      layout: "DashboardLayout",
      requiresAuth: true,
      requiresAdmin: true,
    },
  },
  {
    path: "/Dashboard/InventoryLogs",
    name: "InventoryLogs",
    component: () => import("../views/Dashboard/InventoryLogsPage.vue"),
    meta: {
      layout: "DashboardLayout",
      requiresAuth: true,
      requiresAdmin: true,
    },
  },
  {
    path: "/Dashboard/CouponsDiscounts",
    name: "CouponsDiscounts",
    component: CouponsDiscounts,
    meta: {
      layout: "DashboardLayout",
      requiresAuth: true,
      requiresAdmin: true,
    },
  },
  {
    path: "/Dashboard/MangBanner",
    name: "MangBanner",
    component: MangBanner,
    meta: {
      layout: "DashboardLayout",
      requiresAuth: true,
      requiresAdmin: true,
    },
  },
  {
    path: "/Dashboard/RatingsReviews",
    name: "RatingsReviews",
    component: RatingsReviews,
    meta: {
      layout: "DashboardLayout",
      requiresAuth: true,
      requiresAdmin: true,
    },
  },
  {
    path: "/Dashboard/ShippingZone",
    name: "ShippingZone",
    component: ShippingZone,
    meta: {
      layout: "DashboardLayout",
      requiresAuth: true,
      requiresAdmin: true,
    },
  },
  {
    path: "/Dashboard/StoreSetting",
    name: "StoreSetting",
    component: StoreSetting,
    meta: {
      layout: "DashboardLayout",
      requiresAuth: true,
      requiresAdmin: true,
    },
  },
  {
    path: "/Dashboard/ContactMessage",
    name: "ContactMessage",
    component: ContactMessage,
    meta: {
      layout: "DashboardLayout",
      requiresAuth: true,
      requiresAdmin: true,
    },
  },
  {
    path: "/Dashboard/Financials",
    name: "AdminFinancials",
    component: () => import("../views/Dashboard/FinancialsPage.vue"),
    meta: {
      layout: "DashboardLayout",
      requiresAuth: true,
      requiresAdmin: true,
    },
  },
  {
    path: "/Dashboard/AdvancedReports",
    name: "AdvancedReports",
    component: () => import("../views/Dashboard/AdvancedReportsPage.vue"),
    meta: {
      layout: "DashboardLayout",
      requiresAuth: true,
      requiresAdmin: true,
    },
  },
  {
    path: "/Dashboard/AddTager",
    name: "AddTager",
    component: AddTager,
    meta: {
      layout: "DashboardLayout",
      requiresAuth: true,
      requiresAdmin: true,
    },
  },
  {
    path: "/Dashboard/MangReturns",
    name: "MangReturns",
    component: MangReturns,
    meta: {
      layout: "DashboardLayout",
      requiresAuth: true,
      requiresAdmin: true,
    },
  },
  {
    path: "/Dashboard/MangPurchases",
    name: "MangPurchases",
    component: MangPurchases,
    meta: {
      layout: "DashboardLayout",
      requiresAuth: true,
      requiresAdmin: true,
    },
  },

  {
    path: "/Dashboard/StaffManagement",
    name: "StaffManagement",
    component: StaffManagement,
    meta: {
      layout: "DashboardLayout",
      requiresAuth: true,
      requiresAdmin: true,
    },
  },
  {
    path: "/Dashboard/Profile",
    name: "AdminProfile",
    component: () => import("../views/Dashboard/AdminProfile.vue"),
    meta: {
      layout: "DashboardLayout",
      requiresAuth: true,
      requiresAdmin: true,
    },
  },
  {
    path: "/Dashboard/MangBlog",
    name: "MangBlog",
    component: MangBlog,
    meta: {
      layout: "DashboardLayout",
      requiresAuth: true,
      requiresAdmin: true,
    },
  },
  {
    path: "/Dashboard/MangSEO",
    name: "MangSEO",
    component: () => import("../views/Dashboard/MangSEO.vue"),
    meta: {
      layout: "DashboardLayout",
      requiresAuth: true,
      requiresAdmin: true,
    },
  },
  // صفحة الـ 404
  {
    path: "/:pathMatch(.*)*",
    name: "NotFound",
    component: () => import("../views/NotFound.vue"),
    meta: { hideNavFooter: true },
  },
];

const router = createRouter({
  history: createWebHashHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition;
    }
    return { top: 0 };
  },
});

// --- Token validation cache: نتحقق مرة واحدة فقط عند أول تشغيل ---
let tokenValidated = false;

router.beforeEach(async (to, from, next) => {
  const auth = useAuthStore();
  const token = localStorage.getItem("auth_token");

  // --- التحقق من صحة الـ Token عند أول تنقل فقط ---
  if (token && !tokenValidated) {
    tokenValidated = true;
    try {
      const baseUrl = process.env.VUE_APP_API_URL || "http://127.0.0.1:8000";
      // نستخدم fetch مباشرة هنا لتجنب circular imports مع api.js
      const res = await fetch(`${baseUrl}/api/user`, {
        headers: {
          Authorization: `Bearer ${token}`,
          Accept: "application/json",
        },
      });

      if (res.ok) {
        const data = await res.json();
        // تحديث بيانات المستخدم في الـ store بأحدث ما في الـ backend
        auth.isAuthenticated = true;
        auth.currentUser = data.user;
        localStorage.setItem("user", JSON.stringify(data.user));
      } else {
        // Token منتهي أو غير صالح — تنظيف كامل
        localStorage.removeItem("auth_token");
        localStorage.removeItem("user");
        auth.isAuthenticated = false;
        auth.currentUser = null;
        if (to.meta.requiresAuth) {
          return next("/Auth/LogIn");
        }
        return next();
      }
    } catch {
      // Backend غير متاح — نثق بالـ localStorage مؤقتاً
      if (token) {
        auth.isAuthenticated = true;
        try {
          auth.currentUser = JSON.parse(localStorage.getItem("user"));
        } catch {
          auth.currentUser = null;
        }
      }
    }
  } else if (token && !auth.isAuthenticated) {
    // Token موجود وتم التحقق سابقاً — فقط نزامن auth store
    auth.isAuthenticated = true;
    try {
      auth.currentUser = JSON.parse(localStorage.getItem("user"));
    } catch {
      auth.currentUser = null;
    }
  }

  const isAuthenticated =
    !!localStorage.getItem("auth_token") && auth.isAuthenticated;
  const user = auth.currentUser;

  // 1. منع الزوار من الصفحات المحمية
  if (to.meta.requiresAuth && !isAuthenticated) {
    return next("/Auth/LogIn");
  }

  // 2. منع المسجلين من صفحات الضيف فقط
  if (to.meta.guestOnly && isAuthenticated) {
    return next("/home");
  }

  // 3. redirect الجذر للمسجلين
  if (to.path === "/" && isAuthenticated) {
    return next("/home");
  }

  // 4. منع غير الإداريين من الداشبورد
  if (to.meta.requiresAdmin) {
    if (!user || !["admin", "moderator"].includes(user.role)) {
      return next("/home");
    }
  }

  next();
});

router.afterEach((to) => {
  // 1. Dynamic Title
  const defaultTitle = "ZadStore | منصة التجارة الإلكترونية";
  document.title = to.meta.title ? `${to.meta.title} | ZadStore` : defaultTitle;

  // 2. Dynamic Description
  const defaultDesc =
    "ZadStore - منصتك المتكاملة للتجارة الإلكترونية والتسويق بالعمولة. اكتشف أفضل المنتجات بأفضل الأسعار.";
  const description = to.meta.description || defaultDesc;

  let metaDescription = document.querySelector('meta[name="description"]');
  if (metaDescription) {
    metaDescription.setAttribute("content", description);
  }

  // 3. Open Graph Tags
  const updateOgTag = (property, content) => {
    let tag = document.querySelector(`meta[property="${property}"]`);
    if (tag) tag.setAttribute("content", content);
  };

  updateOgTag("og:title", to.meta.title || "ZadStore");
  updateOgTag("og:description", description);
  updateOgTag("og:url", window.location.href);
});

export default router;
