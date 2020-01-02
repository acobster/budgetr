(ns budgetr.state
  (:require
   [clojure.spec.alpha :as spec]
   [cljs.reader :refer [read-string]]
   [reagent.core :as r]))


(spec/def ::name string?)
(spec/def ::description string?)
(spec/def ::day int?)
(spec/def ::amount int?)

(spec/def ::item (spec/keys :req-un [::name ::description ::day ::amount]))
(spec/def ::items (spec/coll-of ::item :kind vector?))

(spec/def ::app-state (spec/keys :req-un [::items]))


(defonce default-app-state
  {:items [{:id "abc"
            :name "My First Item"
            :description "Edits to this text are saved automatically."
            :day 1
            :amount 100.00}
           {:id "asdfqwerty"
            :name "Item Name"
            :description "Lorem ipsum dolor sit amet"
            :day 1
            :amount 100}
           {:id "wriogwrgajwji"
            :name "Another Item"
            :description "Lorem ipsum dolor sit amet"
            :day 5
            :amount 150}
           {:id "pfovmsjoerjjw"
            :name "Reprehenderit elit"
            :description "Lorem ipsum dolor sit amet"
            :day 7
            :amount 80}
           {:id "nbnmsdhwgweg"
            :name "impedit irure"
            :description "Lorem ipsum dolor sit amet"
            :day 11
            :amount 250}
           {:id "erysbsdfvsfgha"
            :name "in aliquip quo"
            :description "Lorem ipsum dolor sit amet"
            :day 16
            :amount 500}
           {:id "sdfgrmrtnerbebr"
            :name "similique optio consequat"
            :description "Lorem ipsum dolor sit amet"
            :day 21
            :amount 500}
           {:id "zqodjslcndjewuj"
            :name "Last one"
            :description "Lorem ipsum dolor sit amet"
            :day 30
            :amount 150}]
   :selecting? false
   :selection #{}})

(defonce app-state
  (r/atom default-app-state))

(def items (r/cursor app-state [:items]))
(def selection (r/cursor app-state [:selection]))
(def selecting? (r/cursor app-state [:selecting?]))

(comment

  @selecting?
  
  )

(defn selected? [item]
  (contains? @selection (:id item)))
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
  :init-app-state
  [_ _ new-state]
  new-state)

(defmethod handle-action
  :select-item
  [_ state idx]
  (if (:selecting? state)
    (let [items (items-between (:items state)
                              (:selection-start state)
                              idx)]
      (-> state
          (assoc :selecting? false)
          (assoc :selection (set (map :id items)))))
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


(defn persist! [app-state]
  "Persist app-state in localStorage as EDN"
  (->> app-state
       str
       (js/localStorage.setItem "app-state")))


(defn fetch-from-local-storage []
  (some-> "app-state"
          js/localStorage.getItem
          read-string))


(defn emit! [action & values]
  (swap! app-state
         (fn [state]
           (apply handle-action action (concat [state] values))))
  (persist! @app-state))
