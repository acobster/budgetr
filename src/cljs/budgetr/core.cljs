(ns budgetr.core
  (:require
    [budgetr.items :as items]
    [budgetr.routes :as routes]
    [reagent.core :as reagent :refer [atom]]
    [reagent.session :as session]
    [reitit.frontend :as reitit]
    [clerk.core :as clerk]
    [accountant.core :as accountant]))


;; -------------------------
;; Page components


(defn categories-page []
  (fn [] [:span.main
          [:h1 "Categories???"]]))


;; -------------------------
;; Translate routes -> page components

(defn page-for [route]
  (case route
    :categories #'categories-page
    :items #'items/items-list))


;; -------------------------
;; Page mounting component

(defn current-page []
  (fn []
    (let [page (:current-page (session/get :route))]
      [:div
       [:header
        [:p [:a {:href (routes/path-for :items)} "Items"] " | "
         [:a {:href (routes/path-for :categories)} "Categories"]]]
       [page]])))

;; -------------------------
;; Initialize app

(defn mount-root []
  (reagent/render [current-page] (.getElementById js/document "app")))

(defn init! []
  (clerk/initialize!)
  (accountant/configure-navigation!
   {:nav-handler
    (fn [path]
      (let [match (reitit/match-by-path routes/router path)
            current-page (:name (:data  match))
            route-params (:path-params match)]
        (reagent/after-render clerk/after-render!)
        (session/put! :route {:current-page (page-for current-page)
                              :route-params route-params})
        (clerk/navigate-page! path)
        ))
    :path-exists?
    (fn [path]
      (boolean (reitit/match-by-path routes/router path)))})
  (accountant/dispatch-current!)
  (mount-root))
