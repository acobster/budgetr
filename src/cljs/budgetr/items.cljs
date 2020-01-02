(ns budgetr.items
  (:require
    [budgetr.state :as s :refer [app-state]]
    [budgetr.routes :as routes]
    [reagent.core :as r]))


(defn- sum-amounts [items]
  (letfn [(+amount [sum item] (+ sum (int (:amount item))))]
    (reduce +amount 0 items)))


(defn item [i idx]
  (let [selected-class (if (s/selected? i) "i--selected")
        starts-sel-class (if (s/starts-selection? idx) " i--selection-start")
        classes (str selected-class starts-sel-class)]
  [:article {:class classes
             :data-idx idx}
   [:span.i-field.i-name (:name i)]
   [:span.i-field.i-description (:description i)]
   [:span.i-field.i-day
    [:input {:type "number"
             :value (:day i)
             :on-change #(let [v (.-target.value %)
                               new-item (conj i {:day v})]
                           (s/emit! :update-item idx new-item))}]]
   [:span.i-field.i-amount
    [:input {:type "number"
             :value (:amount i)
             :on-change #(let [v (.-target.value %)
                               new-item (conj i {:amount v})]
                           (s/emit! :update-item idx new-item))}]]
   [:div.i-handle {:on-click #(s/emit! :select-item idx)}]]))


(defn help-text [txt]
  [:span.help txt])


(defn summary [items]
  (let [total (sum-amounts @s/items)
        subtotal (sum-amounts (filter s/selected? @s/items))]
      (if @s/selecting?
        [help-text "Click on another item to complete your selection"]
        [:span.emphasized "Expenses: $" subtotal " / $" total " total"])))


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
