(ns budgetr.state-spec
  (:require
    [budgetr.state :as sut]
    [clojure.spec.alpha :as spec]
    [clojure.test.check.clojure-test :refer-macros [defspec]]
    [clojure.test.check.generators :as gen]
    [clojure.test.check.properties :as prop]))


;; TODO generate a bunch of app-states
;; pass them through various action handlers
;; assert they're in the right order, etc

(defn sorted-by-day? [items]
  (let [days (map (comp int :day) items)]
    (= days (sort days))))

(defspec update-item-sorts-items-by-day 100
  (prop/for-all
    [state (spec/gen ::budgetr.state/app-state)]
    (let [idx (rand-int (count (:items state)))
          item (gen/generate (spec/gen ::budgetr.state/item))
          new-state (sut/handle-action :update-item state idx item)]
    (-> new-state
        :items
        sorted-by-day?))))
