(ns budgetr.state
  (:require
    [clojure.spec.alpha :as spec]
    [reagent.core :as r]))


(spec/def ::name string?)
(spec/def ::description string?)
(spec/def ::day int?)
(spec/def ::amount int?)

(spec/def ::item (spec/keys :req-un [::name ::description ::day ::amount]))
(spec/def ::items (spec/coll-of ::item :kind vector?))

(spec/def ::app-state (spec/keys :req-un [::items]))


(defonce app-state
  (r/atom {:items [{:uuid "asdfqwerty"
                    :name "Item Name"
                    :description "Lorem ipsum dolor sit amet"
                    :day 1
                    :amount 100}
                   {:uuid "wriogwrgajwji"
                    :name "Another Item"
                    :description "Lorem ipsum dolor sit amet"
                    :day 5
                    :amount 150}
                   {:uuid "pfovmsjoerjjw"
                    :name "Reprehenderit elit"
                    :description "Lorem ipsum dolor sit amet"
                    :day 7
                    :amount 80}
                   {:uuid "nbnmsdhwgweg"
                    :name "impedit irure"
                    :description "Lorem ipsum dolor sit amet"
                    :day 11
                    :amount 250}
                   {:uuid "erysbsdfvsfgha"
                    :name "in aliquip quo"
                    :description "Lorem ipsum dolor sit amet"
                    :day 16
                    :amount 500}
                   {:uuid "sdfgrmrtnerbebr"
                    :name "similique optio consequat"
                    :description "Lorem ipsum dolor sit amet"
                    :day 21
                    :amount 500}
                   {:uuid "zqodjslcndjewuj"
                    :name "Last one"
                    :description "Lorem ipsum dolor sit amet"
                    :day 30
                    :amount 150}]
           :selecting? false
           :selection #{"wriogwrgajwji" "pfovmsjoerjjw" "nbnmsdhwgweg"}}))

(def items (r/cursor app-state [:items]))
(def selection (r/cursor app-state [:selection]))
(def selecting? (r/cursor app-state [:selecting?]))

(comment

  @selecting?
  
  )

(defn selected? [item]
  (contains? @selection (:uuid item)))
(defn starts-selection? [idx]
  (= idx (:selection-start @app-state)))

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
  :select-item
  [_ state idx]
  (if (:selecting? state)
    (let [items (items-between (:items state)
                              (:selection-start state)
                              idx)]
      (-> state
          (assoc :selecting? false)
          (assoc :selection (set (map :uuid items)))))
    (-> state
        (assoc :selecting? true)
        (assoc :selection-start idx)
        (assoc :selection #{}))))

(defmethod handle-action
  :update-item
  [_ state idx item]
  (-> state
      (assoc-in [:items idx] item)
      (update :items #(vec (sort-by (comp int :day) %)))))



(defn emit! [action & values]
  (swap! app-state
         (fn [state]
           (apply handle-action action (concat [state] values)))))
