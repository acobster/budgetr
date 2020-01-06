(ns budgetr.state
  (:require
   [budgetr.store :as store]
   [clojure.spec.alpha :as spec]
   [reagent.core :as r]))


(spec/def ::name string?)
(spec/def ::description string?)
(spec/def ::day (spec/int-in 1 31))
(spec/def ::amount double?)

(spec/def ::item (spec/keys :req-un [::name ::description ::day ::amount]))
(spec/def ::items (spec/coll-of ::item :kind vector? :distinct true))

(spec/def ::app-state (spec/keys :req-un [::items]))


(defonce default-app-state
  {:items [{:id "abc"
            :name "My First Item"
            :description "Edits to this text are saved automatically."
            :day 1
            :amount 100.00}]
   :selected-range [1 31]})

(defonce app-state
  (r/atom default-app-state))

(def items (r/cursor app-state [:items]))
(def selected-range (r/cursor app-state [:selected-range]))

(defn selected? [item]
  (let [day (:day item)
        foo (js/console.log (clj->js @selected-range))
        [start end] @selected-range]
    (and
     (>= day start)
     (<= day end))))

(defn selected-items []
  (filter selected? @items))



(defn- items-between [items a b]
  (let [start (min a b)
        end   (max a b)
        len   (inc (- end start))]
    (->> items
         (drop start)
         (take len))))


(defmulti handle-action (fn [action _] action))

(defmethod handle-action
  :init-app-state
  [_ _ new-state]
  new-state)

(defmethod handle-action
  :select-day
  [_ state day]
  (if (:selecting? state)
      (-> state
          (assoc-in [:selected-range 1] day)
          (assoc :selecting? false))
    (-> state
        (assoc-in [:selected-range 0] day)
        (assoc :selecting? true)
        (assoc :selection #{}))))

(defmethod handle-action
  :update-item
  [_ state idx k v]
  (-> state
      (assoc-in [:items idx k] v)
      (update :items #(vec (sort-by (comp int :day) %)))))

(defmethod handle-action
  :delete-item
  [_ state idx]
  (letfn [(delete-idx [v idx]
                      (vec (concat (subvec v 0 idx)
                                   (subvec v (min (count v) (inc idx))))))]
    (-> state
        (update :items delete-idx idx))))

(defmethod handle-action
  :create-item
  [_ state item idx]
  (letfn [(insert-at [v item idx]
            (let [[before after] (split-at idx v)]
              (vec (concat before [item] after))))]
    (update state :items insert-at item idx)))


(defn emit! [action & values]
  (swap! app-state
         (fn [state]
           (apply handle-action action (concat [state] values))))
  (store/persist! @app-state))

(comment
  (handle-action :create-item
                 {:items []}
                 {:id 123 :name "" :description "" :day 1 :amount 1.00}
                 0))
