(ns budgetr.items
  (:require
    [budgetr.state :as s :refer [app-state]]
    [budgetr.routes :as routes]
    [reagent.core :as r]))


(defn- sum-amounts [items]
  (letfn [(+amount [sum item] (+ sum (int (:amount item))))]
    (reduce +amount 0 items)))

(defn item [i idx]
  (let [selected-class (if (s/selected? i) "i--selected")]
  [:article {:class selected-class
             :data-idx idx}
   [:span.i-field.i-name
    [:input {:type "text"
             :placeholder "Webcam Porn"
             :value (:name i)
             :on-change #(s/emit! :update-item idx :name (.-target.value %))}]]
   [:span.i-field.i-description
    [:input {:type "text"
             :value (:description i)
             :placeholder "streamate.com"
             :on-change #(s/emit! :update-item idx :description (.-target.value %))}]]
   [:span.i-field.i-day
    [:input {:type "number"
             :value (:day i)
             :min 1
             :max 31
             :on-change #(s/emit! :update-item idx :day (.-target.value %))}]]
   [:span.i-field.i-amount
    [:input {:type "number"
             :value (:amount i)
             :min 1
             :max 1000000
             :on-change #(s/emit! :update-item idx :amount (.-target.value %))}]]
   [:div.i-handle {:on-click #(s/emit! :select-day (:day i))}]
   [:div.i-action {:on-click #(s/emit! :create-item {} idx)} "➕⬆️"]
   [:div.i-action {:on-click #(s/emit! :create-item {} (inc idx))} "➕⬇️"]
   [:div.i-action {:on-click #(s/emit! :delete-item idx)} "❌"]]))


(defn help-text [txt]
  [:span.help txt])


(defn summary [items]
  (let [total (sum-amounts @s/items)
        subtotal (sum-amounts (filter s/selected? @s/items))]
    [:span.emphasized "Expenses: $" subtotal " / $" total " total"]))


(defn items-list []
  (fn []
    [:div.items-list
     [:header
      [:h1 "Budgetr"]]
     [:ul (map-indexed (fn [idx i]
                         ^{:key idx}
                         [:li [item i idx]])
                       @s/items)]
     [:footer
      [summary]]]))
